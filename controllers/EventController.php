<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\Battle;
use app\models\UserBattle;
use app\models\EventBoss;
use app\models\EventParticipants;
use app\models\Chat;

class EventController extends AppController
{
    // Страница центральной площади
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        $activeEvent = $this->getActiveEvent();
        
        $isParticipant = false;
        $inBattle = false;
        $currentBattle = null;
        
        if ($activeEvent && $user) {
            $participant = EventParticipants::find()
                ->where(['event_id' => $activeEvent->id, 'user_id' => $user->id])
                ->one();
            
            if ($participant) {
                $isParticipant = true;
                $inBattle = true;
            }
            
            if ($activeEvent->battle_id) {
                $currentBattle = Battle::findOne(['id' => $activeEvent->battle_id]);
                if ($currentBattle && $currentBattle->started == 2) {
                    $inBattle = false;
                }
            }
        }
        
        return $this->render('index', [
            'user' => $user,
            'activeEvent' => $activeEvent,
            'isParticipant' => $isParticipant,
            'inBattle' => $inBattle,
            'currentBattle' => $currentBattle,
        ]);
    }
    
    // Защитить город - присоединиться к общему бою
    public function actionDefend()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        if (!$user) {
            return $this->asJson(['success' => false, 'error' => 'Пользователь не найден']);
        }
        
        $activeEvent = $this->getActiveEvent();
        
        if (!$activeEvent) {
            return $this->asJson(['success' => false, 'error' => 'Нет активного ивента']);
        }
        
        // ДОПОЛНИТЕЛЬНАЯ ПРОВЕРКА: жив ли босс
        if ($activeEvent->battle_id) {
            $bossBattle = UserBattle::find()
                ->where(['battle_id' => $activeEvent->battle_id, 'bot_id' => $activeEvent->bot_id])
                ->one();
            
            if ($bossBattle && $bossBattle->hp <= 0) {
                // Босс мертв, деактивируем ивент
                $activeEvent->is_active = 0;
                $activeEvent->save(false);
                return $this->asJson(['success' => false, 'error' => 'Босс уже побежден!']);
            }
        }
        
        // Проверяем, не участвует ли уже пользователь
        $existingParticipant = EventParticipants::find()
            ->where(['event_id' => $activeEvent->id, 'user_id' => $user->id])
            ->one();
        
        if ($existingParticipant) {
            return $this->asJson(['success' => false, 'error' => 'Вы уже участвуете в этом ивенте']);
        }
        
        // Проверяем, не в бою ли уже пользователь
        if ($user->in_battle) {
            return $this->asJson(['success' => false, 'error' => 'Вы уже в бою']);
        }
        
        // Получаем или создаём общую битву
        $battle = null;
        if ($activeEvent->battle_id) {
            $battle = Battle::findOne(['id' => $activeEvent->battle_id]);
            if ($battle && $battle->started == 2) {
                $battle = null;
            }
        }
        
        if (!$battle) {
            $bot = User::findOne(['id' => $activeEvent->bot_id]);
            
            $battle = new Battle();
            $battle->start_time = time();
            $battle->type = 1;
            $battle->level = $bot ? $bot->level : 50;
            $battle->started = 1;
            $battle->save(false);
            
            $activeEvent->battle_id = $battle->id;
            $activeEvent->current_hp = $bot ? $bot->health : 5000;
            $activeEvent->save(false);
            
            // Создаём босса в битве
            $botBattle = new UserBattle();
            $botBattle->battle_id = $battle->id;
            $botBattle->bot_id = $activeEvent->bot_id;
            $botBattle->hp = $activeEvent->current_hp;
            $botBattle->komand = 2;
            $botBattle->priority = 1;
            $botBattle->IsAlive = 1;
            $botBattle->shild = 0;
            $botBattle->total_damage = 0;
            $botBattle->save(false);
        }
        
        // Добавляем игрока в битву
        $user->battle_id = $battle->id;
        $user->in_battle = 1;
        $user->save(false);
        
        $maxPriority = UserBattle::find()
            ->where(['battle_id' => $battle->id, 'komand' => 1])
            ->max('priority');
        
        $userBattle = new UserBattle();
        $userBattle->battle_id = $battle->id;
        $userBattle->user_id = $user->id;
        $userBattle->hp = $user->health;
        $userBattle->user_session = $user->session_id;
        $userBattle->komand = 1;
        $userBattle->priority = ($maxPriority ? $maxPriority : 0) + 1;
        $userBattle->IsAlive = 1;
        $userBattle->shild = 0;
        $userBattle->total_damage = 0;
        $userBattle->target = 1;
        $userBattle->save(false);
        
        // Записываем участника ивента
        $participant = new EventParticipants();
        $participant->event_id = $activeEvent->id;
        $participant->user_id = $user->id;
        $participant->battle_id = $battle->id;
        $participant->joined_at = date('Y-m-d H:i:s');
        $participant->save(false);
        
        // WebSocket уведомление
        $this->sendWebSocketReload($user->session_id);
        
        return $this->asJson(['success' => true, 'battle_id' => $battle->id]);
    }
    
    // Получить информацию об ивенте
    public function actionInfo()
    {
        $activeEvent = $this->getActiveEvent();
        
        if (!$activeEvent) {
            return $this->asJson(['success' => false, 'active' => false]);
        }
        
        // Проверяем, жив ли босс
        if ($activeEvent->battle_id) {
            $bossBattle = UserBattle::find()
                ->where(['battle_id' => $activeEvent->battle_id, 'bot_id' => $activeEvent->bot_id])
                ->one();
            
            if ($bossBattle && $bossBattle->hp <= 0) {
                // Босс мертв, деактивируем ивент
                $activeEvent->is_active = 0;
                $activeEvent->save(false);
                return $this->asJson(['success' => false, 'active' => false]);
            }
        }
        
        $participantsCount = EventParticipants::find()
            ->where(['event_id' => $activeEvent->id])
            ->count();
        
        $winnersCount = EventParticipants::find()
            ->where(['event_id' => $activeEvent->id, 'is_winner' => 1])
            ->count();
        
        $bot = User::findOne(['id' => $activeEvent->bot_id]);
        
        // Текущее HP босса
        $currentHp = $activeEvent->current_hp;
        if ($activeEvent->battle_id) {
            $bossBattle = UserBattle::find()
                ->where(['battle_id' => $activeEvent->battle_id, 'bot_id' => $activeEvent->bot_id])
                ->one();
            if ($bossBattle) {
                $currentHp = $bossBattle->hp;
                if ($currentHp != $activeEvent->current_hp) {
                    $activeEvent->current_hp = $currentHp;
                    $activeEvent->save(false);
                }
            }
        }
        
        $endTime = strtotime($activeEvent->end_time);
        $timeLeft = max(0, $endTime - time());
        
        // Если босс мертв, деактивируем ивент
        if ($currentHp <= 0 && $activeEvent->is_active == 1) {
            $activeEvent->is_active = 0;
            $activeEvent->save(false);
            
            // Завершаем битву
            if ($activeEvent->battle_id) {
                $battle = Battle::findOne(['id' => $activeEvent->battle_id]);
                if ($battle && $battle->started != 2) {
                    $battle->started = 2;
                    $battle->save(false);
                }
            }
            
            return $this->asJson(['success' => false, 'active' => false]);
        }
        
        return $this->asJson([
            'success' => true,
            'active' => true,
            'event' => [
                'id' => $activeEvent->id,
                'bot_name' => $bot ? $bot->username : 'Общий враг',
                'bot_max_hp' => $bot ? $bot->health : 0,
                'bot_current_hp' => max(0, $currentHp),
                'bot_damage' => $bot ? $bot->damage : 0,
                'bot_defence' => $bot ? $bot->defence : 0,
                'participants' => $participantsCount,
                'winners' => $winnersCount,
                'time_left' => $timeLeft,
                'end_time' => $activeEvent->end_time,
            ]
        ]);
    }
    
    // Тестовый запуск ивента
    public function actionTestStart()
{
    if (!YII_ENV_DEV) {
        return $this->asJson(['success' => false, 'error' => 'Доступно только в тестовом режиме']);
    }
    
    // Проверяем, есть ли уже активный ивент
    $existingEvent = EventBoss::find()
        ->where(['is_active' => 1])
        ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s')])
        ->one();
    
    if ($existingEvent) {
        return $this->asJson(['success' => false, 'error' => 'Ивент уже активен! Завершите его сначала.']);
    }
    
    $botId = Yii::$app->request->get('bot_id', 16);
    $durationMinutes = Yii::$app->request->get('duration', 10);
    
    $bot = User::findOne(['id' => $botId]);
    if (!$bot) {
        return $this->asJson(['success' => false, 'error' => 'Бот не найден']);
    }
    
    // Деактивируем старые ивенты
    EventBoss::updateAll(['is_active' => 0], ['is_active' => 1]);
    
    $now = new \DateTime();
    $startTime = clone $now;
    $endTime = clone $now;
    $endTime->modify("+{$durationMinutes} minutes");
    
    // СОЗДАЕМ БИТВУ ДЛЯ БОССА СРАЗУ
    $battle = new Battle();
    $battle->start_time = time();
    $battle->type = 1;
    $battle->level = $bot->level;
    $battle->started = 1;
    $battle->save(false);
    
    // Создаем босса в битве
    $bossBattle = new UserBattle();
    $bossBattle->battle_id = $battle->id;
    $bossBattle->bot_id = $botId;
    $bossBattle->hp = $bot->health;
    $bossBattle->komand = 2;
    $bossBattle->priority = 1;
    $bossBattle->IsAlive = 1;
    $bossBattle->shild = 0;
    $bossBattle->total_damage = 0;
    $bossBattle->save(false);
    
    $event = new EventBoss();
    $event->bot_id = $botId;
    $event->event_date = $now->format('Y-m-d');
    $event->start_time = $startTime->format('Y-m-d H:i:s');
    $event->end_time = $endTime->format('Y-m-d H:i:s');
    $event->is_active = 1;
    $event->battle_id = $battle->id;
    $event->current_hp = $bot->health;
    $event->save(false);
    
    return $this->asJson([
        'success' => true,
        'message' => "Ивент запущен до {$endTime->format('H:i:s')}",
        'event' => $event
    ]);
}
    
    // Завершить тестовый ивент
    public function actionTestEnd()
    {
        if (!YII_ENV_DEV) {
            return $this->asJson(['success' => false, 'error' => 'Доступно только в тестовом режиме']);
        }
        
        $affected = EventBoss::updateAll(['is_active' => 0], ['is_active' => 1]);
        
        return $this->asJson(['success' => true, 'message' => "Деактивировано ивентов: {$affected}"]);
    }
    
    // Выдача награды за победу
    public static function giveEventReward($userId, $eventId)
    {
        $participant = EventParticipants::find()
            ->where(['event_id' => $eventId, 'user_id' => $userId, 'reward_given' => 0])
            ->one();
        
        if (!$participant) {
            return false;
        }
        
        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            return false;
        }
        
        $expReward = 500;
        $krReward = 250;
        $pointsReward = 10;
        
        $user->exp += $expReward;
        $user->kr += $krReward;
        $user->points += $pointsReward;
        
        while ($user->exp >= $user->level * 1000) {
            $user->exp -= $user->level * 1000;
            $user->level++;
        }
        
        $user->save(false);
        
        $participant->is_winner = 1;
        $participant->reward_given = 1;
        $participant->save(false);
        
        // Системное сообщение в чат
        $chat = new Chat();
        $chat->message = "🏆 Игрок {$user->username} победил Общего врага и получил +{$expReward} опыта, +{$krReward} KR, +{$pointsReward} очков характеристик!";
        $chat->isPrivate = 0;
        $chat->from_user = 0;
        $chat->to_user = null;
        $chat->create_time = time();
        $chat->save(false);
        
        return true;
    }
    
    // Получить активный ивент
    private function getActiveEvent()
    {
        $now = date('Y-m-d H:i:s');
        
        $event = EventBoss::find()
            ->where(['is_active' => 1])
            ->andWhere(['<=', 'start_time', $now])
            ->andWhere(['>=', 'end_time', $now])
            ->one();
        
        if ($event && $event->battle_id) {
            // Проверяем, жив ли босс
            $bossBattle = UserBattle::find()
                ->where(['battle_id' => $event->battle_id, 'bot_id' => $event->bot_id])
                ->one();
            
            if ($bossBattle && $bossBattle->hp <= 0) {
                // Босс мертв - деактивируем ивент
                $event->is_active = 0;
                $event->save(false);
                
                // Завершаем битву
                $battle = Battle::findOne(['id' => $event->battle_id]);
                if ($battle && $battle->started != 2) {
                    $battle->started = 2;
                    $battle->save(false);
                }
                
                return null;
            }
            
            // Обновляем текущее HP в ивенте
            if ($event->current_hp != $bossBattle->hp) {
                $event->current_hp = $bossBattle->hp;
                $event->save(false);
            }
        }
        
        return $event;
    }
    
    private function sendWebSocketReload($sessionId)
    {
        try {
            $ch = curl_init('http://localhost:8080/ws/reload');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['session_id' => $sessionId]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            // Игнорируем ошибки WebSocket
        }
    }
}