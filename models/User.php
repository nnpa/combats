<?php

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    
        public function init()
    {
        parent::init();
        if (!Yii::$app instanceof \yii\console\Application) {
        // Подписываемся на событие после входа
            Yii::$app->user->on(\yii\web\User::EVENT_AFTER_LOGIN, function ($event) {
                $user = $event->identity;
                $user->session_id = Yii::$app->session->getId();
                $user->save(false, ['session_id']);
            });
        }
    }
    
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    
    // Свойство для формы входа (не хранится в БД)
    public $password;

    public static function tableName()
    {
        return 'user';
    }

    // Правила валидации (добавьте свои при необходимости)
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            ['username', 'unique'],
            ['password', 'safe'],
            ['email', 'string', 'max' => 255],
            [['password_reset_token'], 'unique'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_INACTIVE]],

            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Этот email уже занят.'],
        ];
    }

    // Находит пользователя по username (для логина)
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    // Валидация пароля (сравнивает введенный пароль с хешем из БД)
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    // --- Методы интерфейса IdentityInterface ---

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

public static function findIdentityByAccessToken($token, $type = null)
{
    if (Yii::$app instanceof \yii\console\Application) {
        return null;
    }
    return static::findOne(['auth_key' => $token]);
}

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
 * Устанавливает пароль (хеширует его).
 */
public function setPassword($password)
{
    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
}

/**
 * Генерирует "remember me" ключ аутентификации.
 */
public function generateAuthKey()
{
    $this->auth_key = Yii::$app->security->generateRandomString();
}

// Добавьте эти методы в класс User:
public static function isPasswordResetTokenValid($token)
{
    if (empty($token)) {
        return false;
    }
    $timestamp = (int) substr($token, strrpos($token, '_') + 1);
    $expire = Yii::$app->params['user.passwordResetTokenExpire'];
    return $timestamp + $expire >= time();
}
public function generatePasswordResetToken()
{
    $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
}
public function removePasswordResetToken()
{
    $this->password_reset_token = null;
}
public static function findByPasswordResetToken($token)
{
    if (!static::isPasswordResetTokenValid($token)) {
        return null;
    }
    return static::findOne([
        'password_reset_token' => $token,
        // Можно добавить проверку статуса, если нужно: , 'status' => self::STATUS_ACTIVE
    ]);
}

public static function getActiveUsers()
{
    $twoMinutesAgo = time() - 120;
    return self::find()
        ->where(['>', 'isOnline', $twoMinutesAgo])
        ->orderBy(['username' => SORT_ASC])
        ->all();
}

public function updateOnlineStatus()
{
    $this->isOnline = time();
    $this->save(false, ['isOnline']);
}

public function getClan()
{
    return $this->hasOne(Clan::class, ['id' => 'clan_id']);
}

}