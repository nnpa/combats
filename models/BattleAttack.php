<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle_attack".
 *
 * @property int $id
 * @property int $battle_id
 * @property int $user_id
 * @property int $enemy_id
 * @property int|null $attack
 * @property int $block
 * @property string|null $skill
 * @property int|null $attack_time
 * @property int|null $isBot
 */
class BattleAttack extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'battle_attack';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attack', 'skill', 'attack_time'], 'default', 'value' => null],
            [['block'], 'default', 'value' => 1],
            [['isBot'], 'default', 'value' => 0],
            [['battle_id', 'user_id', 'enemy_id'], 'required'],
            [['battle_id', 'user_id', 'enemy_id', 'attack', 'block', 'attack_time', 'isBot'], 'integer'],
            [['skill'], 'string', 'max' => 255],
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
            'attack' => 'Attack',
            'block' => 'Block',
            'skill' => 'Skill',
            'attack_time' => 'Attack Time',
            'isBot' => 'Is Bot',
        ];
    }

}
