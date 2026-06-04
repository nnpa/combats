<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle".
 *
 * @property int $id
 * @property int|null $started
 * @property int|null $daemon
 * @property int|null $start_time
 * @property int|null $type
 * @property int $level
 * @property int|null $bots
 */
class Battle extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'battle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['started', 'daemon', 'start_time', 'type', 'bots'], 'default', 'value' => null],
            [['started', 'daemon', 'start_time', 'type', 'level', 'bots'], 'integer'],
            [['level'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'started' => 'Started',
            'daemon' => 'Daemon',
            'start_time' => 'Start Time',
            'type' => 'Type',
            'level' => 'Level',
            'bots' => 'Bots',
        ];
    }

}
