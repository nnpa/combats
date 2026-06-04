<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_levels".
 *
 * @property int $id
 * @property int $level
 * @property int $up Номер UP внутри уровня (1,2,3...)
 * @property int $exp_required Опыт для получения этого UP
 * @property int $points_reward Points за этот UP
 * @property int $kr_reward KR за этот UP
 * @property int $total_exp_to_this_up Общий опыт для достижения этого UP
 * @property int $total_points_to_this_up Общее количество points накопленное к этому UP
 * @property int $total_kr_to_this_up
 * @property int $base_hp Базовое HP на этом уровне
 * @property int $base_damage Базовый урон на этом уровне
 */
class UserLevels extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_levels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_kr_to_this_up'], 'default', 'value' => 0],
            [['base_hp'], 'default', 'value' => 100],
            [['base_damage'], 'default', 'value' => 10],
            [['level', 'up', 'exp_required', 'points_reward', 'kr_reward', 'total_exp_to_this_up', 'total_points_to_this_up'], 'required'],
            [['level', 'up', 'exp_required', 'points_reward', 'kr_reward', 'total_exp_to_this_up', 'total_points_to_this_up', 'total_kr_to_this_up', 'base_hp', 'base_damage'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Level',
            'up' => 'Up',
            'exp_required' => 'Exp Required',
            'points_reward' => 'Points Reward',
            'kr_reward' => 'Kr Reward',
            'total_exp_to_this_up' => 'Total Exp To This Up',
            'total_points_to_this_up' => 'Total Points To This Up',
            'total_kr_to_this_up' => 'Total Kr To This Up',
            'base_hp' => 'Base Hp',
            'base_damage' => 'Base Damage',
        ];
    }

}
