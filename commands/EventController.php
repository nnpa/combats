<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\EventBoss;
use app\models\User;
use app\models\Battle;
use app\models\UserBattle;

class EventController extends Controller
{
    /**
     * Запуск ежедневного ивента в 12:00
     * Использование: php yii event/start-daily
     */
    public function actionStartDaily()
    {
        $today = date('Y-m-d');
        
        // Проверяем, не запущен ли уже ивент сегодня
        $existing = EventBoss::find()
            ->where(['event_date' => $today])
            ->one();
        
        if ($existing) {
            echo "[" . date('Y-m-d H:i:s') . "] Ивент уже был запущен сегодня\n";
            return;
        }
        
        // Проверяем, есть ли босс с id=16
        $bot = User::findOne(['id' => 16]);
        if (!$bot) {
            echo "[" . date('Y-m-d H:i:s') . "] Ошибка: Босс с id=16 не найден!\n";
            return;
        }
        
        $now = new \DateTime();
        $startTime = clone $now;
        $endTime = clone $now;
        $endTime->modify('+10 minutes');
        
        // Создаем битву для босса
        $battle = new Battle();
        $battle->start_time = time();
        $battle->type = 1;
        $battle->level = $bot->level;
        $battle->started = 1;
        $battle->save(false);
        
        // Создаем запись босса в битве
        $bossBattle = new UserBattle();
        $bossBattle->battle_id = $battle->id;
        $bossBattle->bot_id = 16;
        $bossBattle->hp = $bot->health;
        $bossBattle->komand = 2;
        $bossBattle->priority = 1;
        $bossBattle->IsAlive = 1;
        $bossBattle->shild = 0;
        $bossBattle->total_damage = 0;
        $bossBattle->save(false);
        
        // Создаем ивент
        $event = new EventBoss();
        $event->bot_id = 16;
        $event->event_date = $today;
        $event->start_time = $startTime->format('Y-m-d H:i:s');
        $event->end_time = $endTime->format('Y-m-d H:i:s');
        $event->is_active = 1;
        $event->battle_id = $battle->id;
        $event->current_hp = $bot->health;
        $event->save(false);
        
        echo "[" . date('Y-m-d H:i:s') . "] Ивент запущен!\n";
        echo "  Битва ID: {$battle->id}\n";
        echo "  Время окончания: {$event->end_time}\n";
        echo "  HP босса: {$bot->health}\n";
    }
    
    /**
     * Завершить все активные ивенты
     * Использование: php yii event/end-all
     */
    public function actionEndAll()
    {
        $events = EventBoss::find()
            ->where(['is_active' => 1])
            ->all();
        
        if (empty($events)) {
            echo "[" . date('Y-m-d H:i:s') . "] Нет активных ивентов\n";
            return;
        }
        
        foreach ($events as $event) {
            $event->is_active = 0;
            $event->save(false);
            echo "[" . date('Y-m-d H:i:s') . "] Завершен ивент #{$event->id}\n";
        }
        
        echo "[" . date('Y-m-d H:i:s') . "] Все ивенты завершены\n";
    }
    
    /**
     * Проверить статус текущего ивента
     * Использование: php yii event/status
     */
    public function actionStatus()
    {
        $now = date('Y-m-d H:i:s');
        
        $event = EventBoss::find()
            ->where(['is_active' => 1])
            ->andWhere(['<=', 'start_time', $now])
            ->andWhere(['>=', 'end_time', $now])
            ->one();
        
        if (!$event) {
            echo "[" . date('Y-m-d H:i:s') . "] Активных ивентов нет\n";
            return;
        }
        
        $bot = User::findOne(['id' => $event->bot_id]);
        $participantsCount = \app\models\EventParticipants::find()
            ->where(['event_id' => $event->id])
            ->count();
        
        // Текущее HP босса
        $currentHp = $event->current_hp;
        if ($event->battle_id) {
            $bossBattle = UserBattle::find()
                ->where(['battle_id' => $event->battle_id, 'bot_id' => $event->bot_id])
                ->one();
            if ($bossBattle) {
                $currentHp = $bossBattle->hp;
            }
        }
        
        echo "[" . date('Y-m-d H:i:s') . "] Статус ивента:\n";
        echo "  ID ивента: {$event->id}\n";
        echo "  Босс: " . ($bot ? $bot->username : 'Неизвестный') . "\n";
        echo "  Время окончания: {$event->end_time}\n";
        echo "  Участников: {$participantsCount}\n";
        echo "  HP босса: {$currentHp}\n";
        echo "  Активен: " . ($event->is_active ? 'Да' : 'Нет') . "\n";
    }
}
//крон задача
//0 14 * * * cd /путь/к/вашему/проекту && php yii event/start-daily >> /var/log/event.log 2>&1
