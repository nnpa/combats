<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_battle".
 *
 * @property int $id
 * @property int|null $battle_id
 * @property string|null $user_session
 * @property int|null $komand
 * @property int|null $IsAlive
 * @property int|null $priority
 * @property int|null $hp
 * @property int|null $bot_id
 * @property int|null $user_id
 * @property int|null $target
 * @property int|null $shild
 * @property int|null $total_damage
 */
class UserBattle extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_battle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['battle_id', 'user_session', 'komand', 'priority', 'hp', 'bot_id', 'user_id', 'shild', 'total_damage'], 'default', 'value' => null],
            [['target'], 'default', 'value' => 1],
            [['battle_id', 'komand', 'IsAlive', 'priority', 'hp', 'bot_id', 'user_id', 'target', 'shild', 'total_damage'], 'integer'],
            [['user_session'], 'string', 'max' => 255],
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
            'user_session' => 'User Session',
            'komand' => 'Komand',
            'IsAlive' => 'Is Alive',
            'priority' => 'Priority',
            'hp' => 'Hp',
            'bot_id' => 'Bot ID',
            'user_id' => 'User ID',
            'target' => 'Target',
            'shild' => 'Shild',
            'total_damage' => 'Total Damage',
        ];
    }

}
