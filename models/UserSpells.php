<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_spells".
 *
 * @property int $id
 * @property int $user_id
 * @property int $spell_id
 */
class UserSpells extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_spells';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'spell_id'], 'required'],
            [['user_id', 'spell_id'], 'integer'],
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
            'spell_id' => 'Spell ID',
        ];
    }

}
