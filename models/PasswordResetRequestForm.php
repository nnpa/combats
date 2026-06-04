<?php
namespace app\models;
use app\components\MailHelper;

use Yii;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    public $email;
    
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Нет пользователя с таким активным email.'
            ],
        ];
    }
    
        public function sendEmail()
     {
         $user = User::findOne([
             'status' => User::STATUS_ACTIVE,
             'email' => $this->email,
         ]);

         if (!$user) {
             return false;
         }

         if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
             $user->generatePasswordResetToken();
             if (!$user->save(false)) {
                 return false;
             }
         }

         $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);

         // Отправляем через PHPMailer
         return MailHelper::sendPasswordReset($this->email, $user->username, $resetLink);
     }
}