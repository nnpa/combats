<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\Pagination;
use app\models\ForumTopics;
use app\models\ForumReplies;

class ForumController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    // Генерация случайного вопроса и ответа
    private function generateCaptcha($type = 'topic')
    {
        $operations = [
            ['question' => '{a} + {b}', 'operator' => '+'],
            ['question' => '{a} - {b}', 'operator' => '-'],
            ['question' => '{a} * {b}', 'operator' => '*'],
        ];
        
        $op = $operations[array_rand($operations)];
        
        if ($op['operator'] == '+') {
            $a = rand(1, 20);
            $b = rand(1, 20);
            $answer = $a + $b;
            $question = str_replace(['{a}', '{b}'], [$a, $b], $op['question']);
        } elseif ($op['operator'] == '-') {
            $a = rand(10, 30);
            $b = rand(1, 9);
            $answer = $a - $b;
            $question = str_replace(['{a}', '{b}'], [$a, $b], $op['question']);
        } else {
            $a = rand(2, 9);
            $b = rand(2, 9);
            $answer = $a * $b;
            $question = str_replace(['{a}', '{b}'], [$a, $b], $op['question']);
        }
        
        $session = Yii::$app->session;
        if ($type == 'topic') {
            $session->set('captcha_answer', $answer);
            $session->set('captcha_question', $question);
        } else {
            $session->set('captcha_answer_reply', $answer);
            $session->set('captcha_question_reply', $question);
        }
        
        return $question;
    }

    public function actionIndex()
    {
        $query = ForumTopics::find()
            ->where(['status' => 1])
            ->with(['user', 'lastReply.user'])
            ->orderBy(['created_at' => SORT_DESC]);
        
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 20,
        ]);
        
        $topics = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        
        return $this->render('index', [
            'topics' => $topics,
            'pagination' => $pagination,
        ]);
    }

public function actionView($id)
{
    $topic = $this->findTopic($id);
    $topic->updateCounters(['views' => 1]);
    
    $query = ForumReplies::find()
        ->where(['topic_id' => $id])
        ->with('user')
        ->orderBy(['created_at' => SORT_ASC]);
    
    $pagination = new Pagination([
        'totalCount' => $query->count(),
        'pageSize' => 20,
    ]);
    
    $replies = $query->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
    
    $newReply = new ForumReplies();
    
    $session = Yii::$app->session;
    $captchaQuestion = $session->get('captcha_question_reply');
    
    if (empty($captchaQuestion)) {
        $captchaQuestion = $this->generateCaptcha('reply');
    }
    
    if ($newReply->load(Yii::$app->request->post())) {
        // ВАЖНО: Устанавливаем topic_id
        $newReply->topic_id = $id;
        
        if ($newReply->validate() && $newReply->save()) {
            $session->remove('captcha_answer_reply');
            $session->remove('captcha_question_reply');
            
            $topic->replies_count = ForumReplies::find()->where(['topic_id' => $id])->count();
            $topic->last_reply_id = $newReply->id;
            $topic->last_reply_time = $newReply->created_at;
            $topic->save();
            
            Yii::$app->session->setFlash('success', 'Ответ добавлен!');
            return $this->refresh();
        } else {
            // Генерируем новую капчу при ошибке
            $captchaQuestion = $this->generateCaptcha('reply');
        }
    }
    
    return $this->render('view', [
        'topic' => $topic,
        'replies' => $replies,
        'pagination' => $pagination,
        'newReply' => $newReply,
        'captchaQuestion' => $captchaQuestion,
    ]);
}

    public function actionCreate()
    {
        $model = new ForumTopics();
        
        // ПОЛУЧАЕМ ТЕКУЩУЮ КАПЧУ ИЗ СЕССИИ
        $session = Yii::$app->session;
        $captchaQuestion = $session->get('captcha_question');
        
        // Если капчи нет в сессии - генерируем новую
        if (empty($captchaQuestion)) {
            $captchaQuestion = $this->generateCaptcha('topic');
        }
        
        if ($model->load(Yii::$app->request->post())) {
            // ДЛЯ ОТЛАДКИ
            Yii::info('POST captcha: ' . Yii::$app->request->post('ForumTopics')['captcha'] ?? 'null', 'captcha');
            Yii::info('Session captcha: ' . $session->get('captcha_answer'), 'captcha');
            
            if ($model->validate() && $model->save()) {
                // Очищаем капчу после успешного сохранения
                $session->remove('captcha_answer');
                $session->remove('captcha_question');
                
                Yii::$app->session->setFlash('success', 'Тема создана!');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // При ошибке - генерируем НОВУЮ капчу
                $captchaQuestion = $this->generateCaptcha('topic');
            }
        }
        
        return $this->render('create', [
            'model' => $model,
            'captchaQuestion' => $captchaQuestion,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findTopic($id);
        
        $currentUser = Yii::$app->user;
        $isAdmin = ($currentUser->identity && $currentUser->identity->username == 'Admin');
        
        if ($model->user_id != $currentUser->id && !$isAdmin) {
            throw new NotFoundHttpException('Access denied');
        }
        
        ForumReplies::deleteAll(['topic_id' => $id]);
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'Тема удалена!');
        return $this->redirect(['index']);
    }

    protected function findTopic($id)
    {
        if (($model = ForumTopics::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Тема не найдена');
    }
}