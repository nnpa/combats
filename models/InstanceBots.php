<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "instance_bots".
 *
 * @property int $id
 * @property int $bot_id
 * @property string $x
 * @property string $y
 * @property int $health
 * @property int $maxHelth
 * @property string $name
 * @property string $color
 * @property string $textureUrl
 * @property int $instance_id
 */
class InstanceBots extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instance_bots';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['textureUrl'], 'default', 'value' => ''],
            [['bot_id', 'x', 'y', 'health', 'maxHelth', 'name', 'color', 'instance_id'], 'required'],
            [['bot_id', 'health', 'maxHelth', 'instance_id'], 'integer'],
            [['x', 'y', 'name', 'color', 'textureUrl'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_id' => 'Bot ID',
            'x' => 'X',
            'y' => 'Y',
            'health' => 'Health',
            'maxHelth' => 'Max Helth',
            'name' => 'Name',
            'color' => 'Color',
            'textureUrl' => 'Texture Url',
            'instance_id' => 'Instance ID',
        ];
    }

}
