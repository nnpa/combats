<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "instance".
 *
 * @property int $id
 * @property int $user_id
 * @property int $level
 * @property string $map
 * @property string $x
 * @property string $y
 * @property int $dir
 * @property int $cooldown
 * @property float $canMove
 */
class Instance extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'level', 'map', 'x', 'y', 'dir', 'cooldown', 'canMove'], 'required'],
            [['user_id', 'level', 'dir', 'cooldown'], 'integer'],
            [['map'], 'string'],
            [['canMove'], 'number'],
            [['x', 'y'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'level' => 'Level',
            'map' => 'Map',
            'x' => 'X',
            'y' => 'Y',
            'dir' => 'Dir',
            'cooldown' => 'Cooldown',
            'canMove' => 'Can Move',
        ];
    }

}
