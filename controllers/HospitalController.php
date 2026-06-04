<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\Inventory as InventoryModel;
use app\models\UserLevels;

class HospitalController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionResetStats()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $user = Yii::$app->user->identity;
            $user = User::findOne(['id' => $user->id]);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Пользователь не найден'];
            }
            
            // 1. Снимаем все предметы с персонажа
            $this->undressAllItems($user);
            
            // 2. Сбрасываем базовые характеристики в 3
            $user->str = 3;
            $user->dex = 3;
            $user->endu = 3;
            $user->inte = 3;
            $user->intu = 3;
            $user->fire = 3;
            $user->water = 3;
            $user->air = 3;
            $user->earth = 3;
            
            // 3. Получаем данные из таблицы user_levels
            $userLevel = UserLevels::findOne([
                'level' => $user->level,
                'up' => $user->up
            ]);
            
            if ($userLevel) {
                // Устанавливаем points из таблицы user_levels
                $user->points = $userLevel->total_points_to_this_up;
            } else {
                // Если запись не найдена, устанавливаем points в 0
                $user->points = 0;
            }
            
            // Сохраняем изменения
            if ($user->save(false)) {
                $transaction->commit();
                return ['success' => true, 'message' => 'Характеристики успешно сброшены'];
            } else {
                $transaction->rollBack();
                return ['success' => false, 'message' => 'Ошибка при сохранении'];
            }
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }
    
    /**
     * Снимает все предметы с персонажа
     */
    private function undressAllItems($user)
    {
        // Список всех слотов экипировки
        $slots = [
            'helm', 'weapon', 'shild', 'chest', 'leg', 'brasers', 'belt',
            'gloves', 'boots', 'earrings', 'amulet', 'ring1', 'ring2', 'ring3'
        ];
        
        foreach ($slots as $slot) {
            $itemId = $user->{$slot};
            
            if ($itemId !== null && $itemId > 0) {
                // Снимаем предмет
                $this->undressItem($user->id, $slot, $itemId);
            }
        }
    }
    
    /**
     * Снимает конкретный предмет
     */
    private function undressItem($userId, $slot, $itemId)
    {
        $item = InventoryModel::findOne(['id' => $itemId]);
        
        if (!$item) {
            // Если предмет не найден, просто очищаем слот
            Yii::$app->db->createCommand()
                ->update('user', [$slot => null], ['id' => $userId])
                ->execute();
            return;
        }
        
        // Отнимаем бонусы предмета
        $stats = ['str', 'dex', 'inte', 'intu', 'endu', 'fire', 'water', 'air', 'earth', 
                  'damage', 'defence', 'health', 'mana', 'crit', 'anticrit', 'mdef', 'evaision', 'aeveision'];
        
        $updateFields = [];
        $params = [];
        
        foreach ($stats as $stat) {
            $statValue = $item->{$stat} ?? 0;
            if ($statValue != 0) {
                $updateFields[] = "`{$stat}` = IFNULL(`{$stat}`, 0) - :{$stat}";
                $params[":{$stat}"] = $statValue;
            }
        }
        
        if (!empty($updateFields)) {
            $sql = "UPDATE `user` SET " . implode(', ', $updateFields) . " WHERE `id` = :userId";
            $params[':userId'] = $userId;
            Yii::$app->db->createCommand($sql, $params)->execute();
        }
        
        // Очищаем слот
        Yii::$app->db->createCommand()
            ->update('user', [$slot => null], ['id' => $userId])
            ->execute();
        
        // Обновляем статус dressed в inventory
        Yii::$app->db->createCommand()
            ->update('inventory', ['dressed' => 0], ['id' => $itemId])
            ->execute();
    }
}
