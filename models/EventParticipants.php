<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "event_participants".
 *
 * @property int $id
 * @property int $event_id
 * @property int $user_id
 * @property int|null $battle_id
 * @property int|null $is_winner
 * @property string $joined_at
 * @property int|null $reward_given
 */
class EventParticipants extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_participants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['battle_id'], 'default', 'value' => null],
            [['reward_given'], 'default', 'value' => 0],
            [['event_id', 'user_id', 'joined_at'], 'required'],
            [['event_id', 'user_id', 'battle_id', 'is_winner', 'reward_given'], 'integer'],
            [['joined_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'user_id' => 'User ID',
            'battle_id' => 'Battle ID',
            'is_winner' => 'Is Winner',
            'joined_at' => 'Joined At',
            'reward_given' => 'Reward Given',
        ];
    }

}
