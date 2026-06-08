<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\Chat;
use app\models\User;

class ChatController extends Controller
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
    
    // Приватный метод для очистки старых сообщений
    private function cleanOldMessages()
    {
        $timeLimit = time() - 600; // 10 минут
        $deleted = Chat::deleteAll(['<', 'create_time', $timeLimit]);
        if ($deleted > 0) {
            Yii::info("Deleted {$deleted} old messages", 'chat');
        }
    }
    
    // Получение сообщений (только те, которые видит пользователь)
    public function actionGetMessages()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Очищаем старые сообщения
        $this->cleanOldMessages();
        
        $lastId = Yii::$app->request->get('last_id', 0);
        $userId = Yii::$app->user->id;
        
        // Фильтрация сообщений:
        // - публичные сообщения (isPrivate = 0) видят все
        // - приватные сообщения (isPrivate = 1) видят только участники переписки
        $query = Chat::find()
            ->where([
                'or',
                ['isPrivate' => 0],  // публичные сообщения
                [
                    'and',
                    ['isPrivate' => 1],
                    ['or',
                        ['from_user' => $userId],
                        ['to_user' => $userId]
                    ]
                ]  // приватные сообщения, где пользователь - отправитель или получатель
            ])
            ->with(['fromUser', 'toUser'])
            ->orderBy(['create_time' => SORT_ASC]);
        
        if ($lastId > 0) {
            $query->andWhere(['>', 'id', $lastId]);
        }
        
        $messages = $query->all();
        
        $result = [];
        foreach ($messages as $msg) {
            // Для приватных сообщений скрываем получателя, если это не участник
            $showToName = false;
            if ($msg->isPrivate == 1) {
                // Показываем получателя только если текущий пользователь - участник переписки
                if ($msg->from_user == $userId || $msg->to_user == $userId) {
                    $showToName = true;
                }
            }
            
            $result[] = [
                'id' => $msg->id,
                'message' => $msg->message,
                'isPrivate' => $msg->isPrivate,
                'from_user' => $msg->from_user,
                'from_name' => $msg->fromUser ? $msg->fromUser->username : 'Unknown',
                'to_user' => $msg->to_user,
                'to_name' => ($showToName && $msg->toUser) ? $msg->toUser->username : null,
                'time' => $msg->create_time,
            ];
        }
        
        // Обновляем онлайн статус текущего пользователя
        $user = User::findOne($userId);
        $user->isOnline = time();
        $user->save(false, ['isOnline']);
        
        return $result;
    }
    
    public function actionSendMessage()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    $userId = Yii::$app->user->id;
    $message = Yii::$app->request->post('message');
    $toUser = Yii::$app->request->post('to_user');
    
    if (empty($message)) {
        return ['success' => false, 'error' => 'Сообщение не может быть пустым'];
    }
    
    if (mb_strlen($message) > 255) {
        return ['success' => false, 'error' => 'Сообщение слишком длинное'];
    }
    
    // Очищаем старые сообщения при отправке
    $this->cleanOldMessages();
    
    $chat = new Chat();
    
    // Проверяем, является ли сообщение приватным
    if ($toUser && $toUser > 0 && $toUser != $userId) {
        // Проверяем, существует ли получатель
        $targetUser = User::findOne($toUser);
        if (!$targetUser) {
            return ['success' => false, 'error' => 'Получатель не найден'];
        }
        
        // Удаляем @ник из начала сообщения, если он там есть
        $pattern = '/^@' . preg_quote($targetUser->username, '/') . '\s+/i';
        $cleanMessage = preg_replace($pattern, '', $message);
        
        $chat->message = $cleanMessage ?: $message;
        $chat->isPrivate = 1;
        $chat->to_user = $toUser;
    } else {
        $chat->message = $message;
        $chat->isPrivate = 0;
        $chat->to_user = null;
    }
    
    $chat->create_time = time();
    $chat->from_user = $userId;
    
    if ($chat->save()) {
        return ['success' => true];
    }
    
    return ['success' => false, 'error' => 'Ошибка отправки'];
}
    
 public function actionGetUsers()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    $timeLimit = time() - 300; // 5 минут
    
    $users = User::find()
        ->select(['user.id', 'user.username', 'user.level', 'user.isOnline', 'clan.img as clan_img'])
        ->leftJoin('clan', 'clan.id = user.clan_id')
        ->where(['>', 'user.isOnline', $timeLimit])
        ->andWhere(['user.bot' => 0])
        ->orderBy(['user.username' => SORT_ASC])
        ->asArray()
        ->all();
    
    $result = [];
    foreach ($users as $user) {
        $result[] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'level' => $user['level'],
            'isOnline' => $user['isOnline'],
            'clan_img' => $user['clan_img'] ?? null, // путь к иконке клана или null
        ];
    }
    
    return $result;
}
    
    // Сохранение состояния чата
    public function actionSaveState()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        $height = Yii::$app->request->post('height');
        $collapsed = Yii::$app->request->post('collapsed');
        
        $user = User::findOne($userId);
        
        if ($height !== null) {
            $user->chat_height = (int)$height;
        }
        if ($collapsed !== null) {
            $user->chat_collapsed = (int)$collapsed;
        }
        
        if ($user->save(false, ['chat_height', 'chat_collapsed'])) {
            return ['success' => true];
        }
        
        return ['success' => false];
    }
    
    // Очистка старых сообщений (можно вызывать по крону)
    public function actionClean()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->cleanOldMessages();
        return ['success' => true, 'message' => 'Old messages cleaned'];
    }
}