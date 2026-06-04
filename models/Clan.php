<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Clan extends ActiveRecord
{
    public $imageFile;
    
    public static function tableName()
    {
        return 'clan';
    }

    public function rules()
    {
        return [
            [['name'], 'required', 'message' => 'Введите название клана'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique', 'message' => 'Клан с таким названием уже существует'],
            [['admin_id', 'created_at'], 'integer'],
            [['img'], 'string', 'max' => 255],
            //[['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024*1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название клана',
            'img' => 'Иконка',
            'admin_id' => 'Глава клана',
            'created_at' => 'Дата создания',
        ];
    }

public function getAdmin()
{
    return $this->hasOne(User::class, ['id' => 'admin_id']);
}

    public function getMembers()
    {
        return $this->hasMany(ClanUser::class, ['clan_id' => 'id'])->where(['status' => 1]);
    }

    public function getRequests()
    {
        return $this->hasMany(ClanUser::class, ['clan_id' => 'id'])->where(['status' => 0]);
    }

    public function getAllMembers()
    {
        return $this->hasMany(ClanUser::class, ['clan_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = time();
            }
            return true;
        }
        return false;
    }
}