<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\models\User;
use app\models\Inventory;
use app\models\UserSpells;

class ForgeController extends Controller
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

    /**
     * Главная страница кузницы
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        // Получаем предметы пользователя (не надетые, не в почте, не артефакты)
        $items = Inventory::findAll([
            'user_id' => $user->id,
            'dressed' => 0,
            'mailed' => 0,
        ]);
        
        // Проверяем наличие заточки (spell_id = 1) в инвентаре пользователя
        $hasSharpening = UserSpells::findOne([
            'user_id' => $user->id,
            'spell_id' => 1
        ]);
        
        return $this->render('index', [
            'user' => $user,
            'items' => $items,
            'hasSharpening' => $hasSharpening
        ]);
    }
    
    /**
     * Заточка предмета
     */
    public function actionSharpen($id)
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        // Проверяем наличие заточки
        $sharpeningSpell = UserSpells::findOne([
            'user_id' => $user->id,
            'spell_id' => 1
        ]);
        
        if (!$sharpeningSpell) {
            Yii::$app->session->setFlash('error', 'У вас нет заклинания заточки!');
            return $this->redirect(['/forge']);
        }
        
        // Находим предмет
        $item = Inventory::findOne([
            'id' => $id,
            'user_id' => $user->id,
            'dressed' => 0,
            'mailed' => 0
        ]);
        
        if (!$item) {
            Yii::$app->session->setFlash('error', 'Предмет не найден!');
            return $this->redirect(['/forge']);
        }
        
        // Проверяем максимальную заточку (+5)
        if ($item->enchant >= 5) {
            Yii::$app->session->setFlash('error', 'Предмет уже заточен на максимум (+5)!');
            return $this->redirect(['/forge']);
        }
        
        $currentEnchant = $item->enchant;
        $newEnchant = $currentEnchant + 1;
        
        // Увеличиваем damage и defence на 2
        if (!is_null($item->damage)) {
            $item->damage += 2;
        }
        if (!is_null($item->defence)) {
            $item->defence += 2;
        }
        
        // Обновляем название
        // Убираем старый суффикс если был
        $baseName = preg_replace('/\s\+\d+$/', '', $item->name);
        $item->name = $baseName . ' +' . $newEnchant;
        
        // Обновляем поле enchant
        $item->enchant = $newEnchant;
        
        if ($item->save(false)) {
            // Удаляем использованное заклинание заточки
            $sharpeningSpell->delete();
            
            Yii::$app->session->setFlash('success', "Вы успешно заточили предмет до +{$newEnchant}! Урон и защита увеличены на 2.");
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при заточке предмета.');
        }
        
        return $this->redirect(['/forge']);
    }
    
    /**
     * Гравировка предмета
     */
    public function actionEngrave()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        $itemId = Yii::$app->request->post('item_id');
        $engravingText = Yii::$app->request->post('engraving_text');
        
        // Проверяем наличие KR (20 KR)
        if ($user->kr < 20) {
            Yii::$app->session->setFlash('error', 'Недостаточно KR! Гравировка стоит 20 KR.');
            return $this->redirect(['/forge']);
        }
        
        // Проверяем длину текста
        if (mb_strlen($engravingText) > 20) {
            Yii::$app->session->setFlash('error', 'Текст гравировки не должен превышать 20 символов.');
            return $this->redirect(['/forge']);
        }
        
        if (empty($engravingText)) {
            Yii::$app->session->setFlash('error', 'Введите текст гравировки.');
            return $this->redirect(['/forge']);
        }
        
        // Находим предмет
        $item = Inventory::findOne([
            'id' => $itemId,
            'user_id' => $user->id,
            'dressed' => 0,
            'mailed' => 0
        ]);
        
        if (!$item) {
            Yii::$app->session->setFlash('error', 'Предмет не найден!');
            return $this->redirect(['/forge']);
        }
        
        // Сохраняем гравировку в description
        $item->description = $engravingText;
        
        if ($item->save(false)) {
            // Списываем KR
            $user->kr -= 20;
            $user->save(false);
            
            Yii::$app->session->setFlash('success', "Гравировка «{$engravingText}» успешно нанесена на предмет «{$item->name}»!");
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при нанесении гравировки.');
        }
        
        return $this->redirect(['/forge']);
    }
}