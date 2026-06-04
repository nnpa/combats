<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_elexir".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $user_id
 * @property string $img
 * @property int $use_time
 */
class UserElexir extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_elexir';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'user_id', 'img', 'use_time'], 'required'],
            [['user_id', 'use_time'], 'integer'],
            [['name', 'type', 'img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'user_id' => 'User ID',
            'img' => 'Img',
            'use_time' => 'Use Time',
        ];
    }

}
