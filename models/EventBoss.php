<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class EventBoss extends ActiveRecord
{
    public static function tableName()
    {
        return 'event_boss';
    }

    public function rules()
    {
        return [
            [['bot_id', 'event_date', 'start_time', 'end_time'], 'required'],
            [['bot_id', 'is_active', 'battle_id', 'current_hp'], 'integer'],
            [['event_date', 'start_time', 'end_time', 'created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_id' => 'ID босса',
            'event_date' => 'Дата ивента',
            'start_time' => 'Время начала',
            'end_time' => 'Время окончания',
            'is_active' => 'Активен',
            'battle_id' => 'ID битвы',
            'current_hp' => 'Текущее HP',
            'created_at' => 'Создан',
        ];
    }

    /**
     * Связь с боссом (пользователем)
     */
    public function getBot()
    {
        return $this->hasOne(User::class, ['id' => 'bot_id']);
    }

    /**
     * Связь с участниками ивента
     */
    public function getParticipants()
    {
        return $this->hasMany(EventParticipants::class, ['event_id' => 'id']);
    }

    /**
     * Связь с битвой
     */
    public function getBattle()
    {
        return $this->hasOne(Battle::class, ['id' => 'battle_id']);
    }

    /**
     * Получить активный ивент на текущий момент
     */
    public static function getActiveEvent()
    {
        $now = date('Y-m-d H:i:s');
        
        return self::find()
            ->where(['is_active' => 1])
            ->andWhere(['<=', 'start_time', $now])
            ->andWhere(['>=', 'end_time', $now])
            ->one();
    }

    /**
     * Проверить, жив ли босс
     */
    public function isBossAlive()
    {
        if (!$this->battle_id) {
            return false;
        }
        
        $bossBattle = UserBattle::find()
            ->where(['battle_id' => $this->battle_id, 'bot_id' => $this->bot_id])
            ->one();
        
        return $bossBattle && $bossBattle->hp > 0;
    }

    /**
     * Получить текущее HP босса
     */
    public function getCurrentHp()
    {
        if ($this->current_hp !== null && $this->current_hp > 0) {
            return $this->current_hp;
        }
        
        if ($this->battle_id) {
            $bossBattle = UserBattle::find()
                ->where(['battle_id' => $this->battle_id, 'bot_id' => $this->bot_id])
                ->one();
            
            if ($bossBattle) {
                return $bossBattle->hp;
            }
        }
        
        $bot = User::findOne($this->bot_id);
        return $bot ? $bot->health : 0;
    }

    /**
     * Получить количество участников ивента
     */
    public function getParticipantsCount()
    {
        return EventParticipants::find()
            ->where(['event_id' => $this->id])
            ->count();
    }

    /**
     * Получить количество победителей
     */
    public function getWinnersCount()
    {
        return EventParticipants::find()
            ->where(['event_id' => $this->id, 'is_winner' => 1])
            ->count();
    }

    /**
     * Деактивировать ивент
     */
    public function deactivate()
    {
        $this->is_active = 0;
        return $this->save(false);
    }

    /**
     * Создать ивент
     */
    public static function createEvent($botId, $durationMinutes = 10)
    {
        // Деактивируем старые ивенты
        self::updateAll(['is_active' => 0], ['is_active' => 1]);
        
        $bot = User::findOne($botId);
        if (!$bot) {
            return null;
        }
        
        $now = new \DateTime();
        $startTime = clone $now;
        $endTime = clone $now;
        $endTime->modify("+{$durationMinutes} minutes");
        
        // Создаем битву
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
        
        // Создаем ивент
        $event = new self();
        $event->bot_id = $botId;
        $event->event_date = $now->format('Y-m-d');
        $event->start_time = $startTime->format('Y-m-d H:i:s');
        $event->end_time = $endTime->format('Y-m-d H:i:s');
        $event->is_active = 1;
        $event->battle_id = $battle->id;
        $event->current_hp = $bot->health;
        $event->save(false);
        
        return $event;
    }
}