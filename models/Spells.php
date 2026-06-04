<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "spells".
 *
 * @property int $id
 * @property string $name
 * @property int $img
 */
class Spells extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'spells';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'img'], 'required'],
            [['id', 'img'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'img' => 'Img',
        ];
    }

}
