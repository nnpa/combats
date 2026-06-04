<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invetory_elexir".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $img
 * @property int $user_id
 */
class InvetoryElexir extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invetory_elexir';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'img', 'user_id'], 'required'],
            [['user_id'], 'integer'],
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
            'img' => 'Img',
            'user_id' => 'User ID',
        ];
    }

}
