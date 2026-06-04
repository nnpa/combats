<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "elexir".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $cost
 * @property string $img
 */
class Elexir extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'elexir';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type', 'cost', 'img'], 'required'],
            [['cost'], 'integer'],
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
            'cost' => 'Cost',
            'img' => 'Img',
        ];
    }

}
