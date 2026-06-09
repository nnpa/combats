<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\User;
use app\models\UserSpells;
use app\models\Battle;
use app\models\UserBattle;

class SpellController extends AppController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    private function hasAttackSpell($userId)
    {
        return UserSpells::findOne(['user_id' => $userId, 'spell_id' => 2]) !== null;
    }
    
    // Удаляем заклинание после использования
    private function removeAttackSpell($userId)
    {
        $spell = UserSpells::findOne(['user_id' => $userId, 'spell_id' => 2]);
        if ($spell) {
            return $spell->delete();
        }
        return false;
    }

    public function actionAttackForm()
    {
        $userId = Yii::$app->user->id;
        
        if (!$this->hasAttackSpell($userId)) {
            Yii::$app->session->setFlash('error', 'У вас нет заклинания "Нападение"!');
            return $this->redirect(['/inventory/index']);
        }
        
        return $this->renderPartial('attack-modal');
    }

    public function actionCheckPlayer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $username = Yii::$app->request->post('username');
        $attackerId = Yii::$app->user->id;
        $attacker = User::findOne($attackerId);
        
        if (!$username) {
            return ['success' => false, 'error' => 'Введите имя игрока'];
        }
        
        $target = User::findOne(['username' => $username]);
        
        if (!$target) {
            return ['success' => false, 'error' => 'Игрок не найден!'];
        }
        
        if ($target->id == $attackerId) {
            return ['success' => false, 'error' => 'Нельзя напасть на самого себя!'];
        }
        
        if ($target->bot == 1) {
            return ['success' => false, 'error' => 'Нельзя напасть на бота!'];
        }
        
        if ($target->in_battle == 1) {
            return ['success' => false, 'error' => '❌ Противник уже в бою! Нельзя напасть на игрока, который участвует в битве.'];
        }
        
        if ($attacker->in_battle == 1) {
            return ['success' => false, 'error' => '❌ Вы уже в бою! Сначала завершите текущий бой.'];
        }
        
        return [
            'success' => true,
            'player' => [
                'username' => $target->username,
                'level' => $target->level,
                'health' => $target->health,
                'damage' => $target->damage,
                'defence' => $target->defence,
                'avatar' => $target->ava,
            ]
        ];
    }

    public function actionAttack()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        $targetUsername = Yii::$app->request->post('username');
        
        if (!$this->hasAttackSpell($userId)) {
            return ['success' => false, 'error' => 'У вас нет заклинания "Нападение"!'];
        }
        
        $target = User::findOne(['username' => $targetUsername]);
        if (!$target) {
            return ['success' => false, 'error' => 'Игрок не найден!'];
        }
        
        if ($target->id == $userId) {
            return ['success' => false, 'error' => 'Нельзя напасть на самого себя!'];
        }
        
        if ($target->bot == 1) {
            return ['success' => false, 'error' => 'Нельзя напасть на бота!'];
        }
        
        // Проверяем, в бою ли противник
        if ($target->in_battle == 1) {
            return ['success' => false, 'error' => 'Противник уже в бою! Нельзя напасть на игрока, который участвует в битве.'];
        }
        
        // Проверяем, в бою ли сам атакующий
        $attacker = User::findOne($userId);
        if ($attacker->in_battle == 1) {
            return ['success' => false, 'error' => 'Вы уже в бою! Сначала завершите текущий бой.'];
        }
        
        // Начинаем новый бой
        $result = $this->startNewBattle($userId, $target->id);
        
        // Если бой успешно начат - удаляем заклинание
        if ($result['success']) {
            $this->removeAttackSpell($userId);
        }
        
        return $result;
    }
    
    private function startNewBattle($attackerId, $targetId)
    {
        // Принудительно получаем свежие данные из БД
        $attacker = User::find()->where(['id' => $attackerId])->one();
        $target = User::find()->where(['id' => $targetId])->one();
        
        if (!$attacker || !$target) {
            return ['success' => false, 'error' => 'Игроки не найдены!'];
        }
        
        // Проверяем, не вступили ли игроки в бой за время проверки
        if ($attacker->in_battle == 1) {
            return ['success' => false, 'error' => 'Вы уже в бою!'];
        }
        
        if ($target->in_battle == 1) {
            return ['success' => false, 'error' => 'Противник уже в бою!'];
        }
        
        // Создаем битву
        $battle = new Battle();
        $battle->start_time = time();
        $battle->type = 2;
        $battle->level = max($attacker->level, $target->level);
        $battle->started = 1;
        $battle->save(false);
        
        $battleId = $battle->id;
        
        // Транзакция для безопасности
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Обновляем battle_id у атакующего
            Yii::$app->db->createCommand()
                ->update('user', [
                    'battle_id' => $battleId, 
                    'in_battle' => 1
                ], ['id' => $attackerId])
                ->execute();
            
            // Обновляем battle_id у цели
            Yii::$app->db->createCommand()
                ->update('user', [
                    'battle_id' => $battleId, 
                    'in_battle' => 1
                ], ['id' => $targetId])
                ->execute();
            
            // СОЗДАЕМ УЧАСТНИКА - АТАКУЮЩИЙ
            $attackerBattle = new UserBattle();
            $attackerBattle->battle_id = $battleId;
            $attackerBattle->user_id = $attackerId;
            $attackerBattle->bot_id = null;
            $attackerBattle->hp = $attacker->health;
            $attackerBattle->user_session = $attacker->session_id;
            $attackerBattle->komand = 1;
            $attackerBattle->priority = 1;
            $attackerBattle->IsAlive = 1;
            $attackerBattle->shild = 0;
            $attackerBattle->total_damage = 0;
            $attackerBattle->target = 2;
            $attackerBattle->save(false);
            
            // СОЗДАЕМ УЧАСТНИКА - ЦЕЛЬ
            $targetBattle = new UserBattle();
            $targetBattle->battle_id = $battleId;
            $targetBattle->user_id = $targetId;
            $targetBattle->bot_id = null;
            $targetBattle->hp = $target->health;
            $targetBattle->user_session = $target->session_id;
            $targetBattle->komand = 2;
            $targetBattle->priority = 2;
            $targetBattle->IsAlive = 1;
            $targetBattle->shild = 0;
            $targetBattle->total_damage = 0;
            $targetBattle->target = 1;
            $targetBattle->save(false);
            
            $transaction->commit();
            
            return [
                'success' => true, 
                'message' => 'Бой начат! Заклинание использовано.', 
                'redirect' => '/battle/battle'
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'error' => 'Ошибка при создании боя: ' . $e->getMessage()];
        }
    }
}