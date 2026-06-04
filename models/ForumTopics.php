<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ForumTopics extends ActiveRecord
{
    public $captcha;
    public $captcha_question;
    
    public static function tableName()
    {
        return 'forum_topics';
    }

    public function rules()
    {
        return [
            [['title', 'content'], 'required', 'message' => 'Заполните это поле'],
            [['title'], 'string', 'max' => 255],
            [['content'], 'string'],
            [['user_id', 'views', 'replies_count', 'status', 'created_at', 'updated_at', 'last_reply_id', 'last_reply_time'], 'integer'],
            ['title', 'trim'],
            ['captcha', 'required', 'message' => 'Введите ответ на вопрос'],
            ['captcha', 'validateCaptcha'],
        ];
    }
    
    // Валидация капчи
 public function validateCaptcha($attribute, $params)
{
    $session = Yii::$app->session;
    $correctAnswer = $session->get('captcha_answer');
    $userAnswer = trim((string)$this->$attribute);
    
    if (empty($correctAnswer)) {
        $this->addError($attribute, 'Ошибка капчи, попробуйте снова');
        return;
    }
    
    // Приводим к одному типу (целое число)
    if ((int)$userAnswer !== (int)$correctAnswer) {
        $this->addError($attribute, 'Ответ неверный! Попробуйте еще раз');
    }
}


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название темы',
            'content' => 'Содержание',
            'user_id' => 'Автор',
            'views' => 'Просмотры',
            'replies_count' => 'Ответы',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'captcha' => 'Код подтверждения',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getReplies()
    {
        return $this->hasMany(ForumReplies::class, ['topic_id' => 'id'])->orderBy(['created_at' => SORT_ASC]);
    }

    public function getLastReply()
    {
        return $this->hasOne(ForumReplies::class, ['id' => 'last_reply_id']);
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
}