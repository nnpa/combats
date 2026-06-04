<?php
namespace app\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use app\models\User;

class ResetPasswordForm extends Model
{
    public $password;
    
    private $_user;
    
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Токен сброса пароля не может быть пустым.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        
        if (!$this->_user) {
            throw new InvalidArgumentException('Неверный токен сброса пароля.');
        }
        
        parent::__construct($config);
    }
    
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }
    
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        
        return $user->save(false);
    }
}