<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ForumReplies extends ActiveRecord
{
    public $captcha;
    
    public static function tableName()
    {
        return 'forum_replies';
    }

    public function rules()
    {
        return [
            [['content'], 'required', 'message' => 'Введите текст ответа'],
            [['content'], 'string'],
            [['topic_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
           // ['captcha', 'required', 'message' => 'Введите ответ на вопрос'],
           // ['captcha', 'validateCaptcha'],
        ];
    }
    
    // Валидация капчи
    public function validateCaptcha($attribute, $params)
    {
        $session = Yii::$app->session;
        $correctAnswer = $session->get('captcha_answer_reply');
        $userAnswer = trim((string)$this->$attribute);
        
        // ОТЛАДКА
        Yii::info("Validating captcha - User: '{$userAnswer}', Correct: '{$correctAnswer}'", 'captcha');
        
        if (empty($correctAnswer)) {
            $this->addError($attribute, 'Ошибка капчи, попробуйте снова');
            return;
        }
        
        // Сравниваем как строки, обрезая пробелы
        if (trim($userAnswer) !== trim((string)$correctAnswer)) {
            $this->addError($attribute, 'Ответ неверный! Попробуйте еще раз');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'topic_id' => 'Тема',
            'content' => 'Ответ',
            'user_id' => 'Автор',
            'created_at' => 'Создано',
            'captcha' => 'Код подтверждения',
        ];
    }

    public function getTopic()
    {
        return $this->hasOne(ForumTopics::class, ['id' => 'topic_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->user_id = Yii::$app->user->id;
                $this->created_at = time();
            }
            $this->updated_at = time();
            return true;
        }
        return false;
    }
    
    // НЕ ДОБАВЛЯЙТЕ afterSave ДЛЯ ОЧИСТКИ КАПЧИ!
    // Очистка должна быть в контроллере
}