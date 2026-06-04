<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ClanUser extends ActiveRecord
{
    public static function tableName()
    {
        return 'clan_user';
    }
    
    public function rules()
    {
        return [
            [['clan_id', 'user_id', 'status', 'created_at'], 'required'],
            [['clan_id', 'user_id', 'status', 'created_at'], 'integer'],
            [['description'], 'string', 'max' => 255],
        ];
    }
    
    // Связь с пользователем
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    // Связь с кланом
    public function getClan()
    {
        return $this->hasOne(Clan::class, ['id' => 'clan_id']);
    }
}