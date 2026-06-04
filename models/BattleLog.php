<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle_log".
 *
 * @property int $id
 * @property int $battle_id
 * @property int $user_id
 * @property int $enemy_id
 * @property string $log
 * @property int $attack_time
 */
class BattleLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'battle_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['battle_id', 'user_id', 'enemy_id', 'log', 'attack_time'], 'required'],
            [['battle_id', 'user_id', 'enemy_id', 'attack_time'], 'integer'],
            [['log'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'battle_id' => 'Battle ID',
            'user_id' => 'User ID',
            'enemy_id' => 'Enemy ID',
            'log' => 'Log',
            'attack_time' => 'Attack Time',
        ];
    }

}
