<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Mailed extends ActiveRecord
{
    public static function tableName()
    {
        return 'mailed';
    }
    
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'item_id', 'cost', 'created_time', 'complited'], 'required'],
            [['from_user_id', 'to_user_id', 'item_id', 'cost', 'created_time', 'complited'], 'integer'],
        ];
    }
    
    // Связь с отправителем
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }
    
    // Связь с получателем
    public function getToUser()
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id']);
    }
    
    // Связь с предметом
    public function getItem()
    {
        return $this->hasOne(Inventory::class, ['id' => 'item_id']);
    }
}