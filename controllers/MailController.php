<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Mailed;
use app\models\Inventory;

class MailController extends AppController
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

    // Главная страница почты
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        
        $incomingMails = Mailed::find()
            ->where(['to_user_id' => $userId, 'complited' => 0])
            ->with(['fromUser', 'item'])
            ->orderBy(['created_time' => SORT_DESC])
            ->all();
        
        return $this->render('index', [
            'incomingMails' => $incomingMails,
        ]);
    }

    // AJAX проверка существования пользователя
// AJAX проверка существования пользователя (поддержка GET и POST)
public function actionCheckuser()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    $username = Yii::$app->request->get('username');
    
    if (!$username) {
        $username = Yii::$app->request->post('username');
    }
    
    if (!$username) {
        return ['success' => false, 'message' => 'Введите имя пользователя'];
    }
    
    $user = User::findOne(['username' => $username]);
    
    if ($user && $user->id != Yii::$app->user->id) {
        return ['success' => true, 'user_id' => $user->id];
    } elseif ($user && $user->id == Yii::$app->user->id) {
        return ['success' => false, 'message' => 'Нельзя отправить предмет самому себе'];
    } else {
        return ['success' => false, 'message' => 'Пользователь не найден'];
    }
}

    // Получение списка доступных предметов
    public function actionGetitems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        
        $items = Inventory::find()
            ->where(['user_id' => $userId, 'dressed' => 0, 'mailed' => 0,'shoped' => 0])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        
        $itemsList = [];
        foreach ($items as $item) {
            $itemsList[] = [
                'id' => $item->id,
                'name' => $item->name,
                'img' => $item->img,
                'cost' => $item->cost,
            ];
        }
        
        return $itemsList;
    }

    // Отправка письма
    public function actionSend()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    $request = Yii::$app->request;
    $toUsername = $request->post('to_username');
    $itemId = $request->post('item_id');
    $price = $request->post('price');
    
    if (!$toUsername || !$itemId || $price === null) {
        return ['success' => false, 'message' => 'Заполните все поля'];
    }
    
    $toUser = User::findOne(['username' => $toUsername]);
    if (!$toUser) {
        return ['success' => false, 'message' => 'Пользователь не найден'];
    }
    
    if ($toUser->id == Yii::$app->user->id) {
        return ['success' => false, 'message' => 'Нельзя отправить предмет самому себе'];
    }
    
    $item = Inventory::findOne([
        'id' => $itemId,
        'user_id' => Yii::$app->user->id,
        'dressed' => 0,
        'mailed' => 0
    ]);
    
    if (!$item) {
        return ['success' => false, 'message' => 'Предмет не найден или уже отправлен'];
    }
    
    $mail = new Mailed();
    $mail->from_user_id = Yii::$app->user->id;
    $mail->to_user_id = $toUser->id;
    $mail->item_id = $itemId;
    $mail->cost = $price;
    $mail->created_time = time();
    $mail->complited = 0;
    
    if ($mail->save()) {
        // Проверяем что сохранилось
        $item->mailed = 1;
        if ($item->save()) {
            return ['success' => true, 'message' => 'Письмо успешно отправлено'];
        } else {
            return ['success' => false, 'message' => 'Предмет не обновлен: ' . json_encode($item->errors)];
        }
    }
    
    return ['success' => false, 'message' => 'Ошибка при отправке'];
}

    // Принять предмет
    public function actionAccept()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $mailId = Yii::$app->request->post('mail_id');
        $userId = Yii::$app->user->id;
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $mail = Mailed::findOne([
                'id' => $mailId,
                'to_user_id' => $userId,
                'complited' => 0
            ]);
            
            if (!$mail) {
                return ['success' => false, 'message' => 'Письмо не найдено'];
            }
            
            $user = User::findOne($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'Пользователь не найден'];
            }
            
            $item = Inventory::findOne($mail->item_id);
            if (!$item) {
                return ['success' => false, 'message' => 'Предмет не найден'];
            }
            
            if ($user->kr < $mail->cost) {
                return ['success' => false, 'message' => 'Недостаточно средств'];
            }
            
            $user->kr -= $mail->cost;
            $user->save(false);
            
            if ($mail->cost > 0) {
                $fromUser = User::findOne($mail->from_user_id);
                if ($fromUser) {
                    $fromUser->kr += $mail->cost;
                    $fromUser->save(false);
                }
            }
            
            $item->user_id = $userId;
            $item->mailed = 0;
            $item->save(false);
            
            $mail->complited = 1;
            $mail->save(false);
            
            $transaction->commit();
            
            return ['success' => true, 'message' => 'Предмет успешно получен!'];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }
    
    // Отклонить предмет
    public function actionReject()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $mailId = Yii::$app->request->post('mail_id');
        $userId = Yii::$app->user->id;
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $mail = Mailed::findOne([
                'id' => $mailId,
                'to_user_id' => $userId,
                'complited' => 0
            ]);
            
            if (!$mail) {
                return ['success' => false, 'message' => 'Письмо не найдено'];
            }
            
            $item = Inventory::findOne($mail->item_id);
            
            if ($item) {
                $item->user_id = $mail->from_user_id;
                $item->mailed = 0;
                $item->save(false);
            }
            
            $mail->delete();
            
            $transaction->commit();
            
            return ['success' => true, 'message' => 'Письмо отклонено'];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }
}