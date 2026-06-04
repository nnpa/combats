<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory".
 *
 * @property int $id
 * @property int $user_id
 * @property int $item_id
 */
class Inventory extends \yii\db\ActiveRecord
{

public function getAuction()
{
    return $this->hasOne(Auction::class, ['item_id' => 'id']);
}
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'item_id'], 'required'],
            [['user_id', 'item_id'], 'integer'],
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
            'item_id' => 'Item ID',
        ];
    }
    public function getUser()
        {
            return $this->hasOne(User::class, ['id' => 'user_id']);
        }

        // Получить доступные предметы для отправки
        public static function getAvailableItems($userId)
        {
            return self::find()
                ->where(['user_id' => $userId, 'dressed' => 0, 'mailed' => 0,'shoped' => 0])
                ->all();
        }
        
        public function getImageUrl()
    {
        if ($this->img && !empty($this->img)) {
            // Если путь уже начинается с /img/items/ или http, возвращаем как есть
            if (strpos($this->img, '/img/items/') === 0 || strpos($this->img, 'http') === 0) {
                return $this->img;
            }
            // Добавляем /img/items/ в начало
            return '/img/items/' . ltrim($this->img, '/');
        }
        // Картинка по умолчанию
        return '/img/items/default.png';
    }
}
