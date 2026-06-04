<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Auction extends ActiveRecord
{
    public static function tableName()
    {
        return 'auction';
    }
    
    public function rules()
    {
        return [
            [['user_id', 'item_id', 'cost', 'create_time'], 'required'],
            [['user_id', 'item_id', 'cost', 'create_time'], 'integer'],
        ];
    }
    
    // Связь с пользователем (продавец)
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    // Связь с предметом в инвентаре
    public function getInventory()
    {
        return $this->hasOne(Inventory::class, ['id' => 'item_id']);
    }
    
    // Связь с предметом (для получения характеристик)
    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'item_id']);
    }
}