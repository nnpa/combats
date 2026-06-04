<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Chat extends ActiveRecord
{
    public static function tableName()
    {
        return 'chat';
    }
    
    public function rules()
    {
        return [
            [['message', 'create_time'], 'required'],
            [['isPrivate', 'from_user', 'to_user', 'create_time'], 'integer'],
            [['message'], 'string', 'max' => 255],
        ];
    }
    
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user']);
    }
    
    public function getToUser()
    {
        return $this->hasOne(User::class, ['id' => 'to_user']);
    }
}