<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class AppController extends Controller
{
    protected function publicActions()
    {
        return [
            'login',
            'signup',
            'requestpasswordreset',
            'reset-password',
            'verify-email',
            'error',
            'captcha',
            'logout', // Добавляем logout в публичные действия
        ];
    }
    
    /**
     * Поведения для контроля доступа
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    // Публичные действия доступны всем
                    [
                        'allow' => true,
                        'actions' => $this->publicActions(),
                        'roles' => ['?', '@'],
                    ],
                    // Все остальные действия только для авторизованных
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if ($action->id !== 'logout') { // Не показываем ошибку при выходе
                        Yii::$app->session->setFlash('error', 'Для доступа к этой странице необходимо авторизоваться.');
                    }
                    return $this->redirect(['/site/login']);
                },
            ],
        ];
    }
    
    // Этот метод выполнится перед любым action в этом контроллере
    public function beforeAction($action)
    {
                $this->enableCsrfValidation = false;

        if (in_array($action->id, $this->publicActions())) {
             return parent::beforeAction($action);
        }
    
        
        // Пропускаем публичные действия (чтобы не было цикла)
        if (in_array($action->id, $this->publicActions())) {
            return parent::beforeAction($action);
        }
        
        $user = Yii::$app->user->identity;
        
        if (!is_null($user)) {
            $user->isOnline = time();
            $user->save(false);
            
            $request = Yii::$app->request;
            $route = $request->getPathInfo(); 
            
            // Массив разрешенных методов (контроллер/метод)
            $allowedActions = [
                'battle/battle',
                'battle/attack',
                'battle/skill',
                "dungeon/saveplayer",
                "dungeon/exit",
                "dungeon/index",
                "dungeon/saveplayer",
                "dungeon/loadplayer",      // ДОБАВИТЬ
                "dungeon/loadmap",         // ДОБАВИТЬ
                "dungeon/loadmonsters",    // ДОБАВИТЬ
                "site/info",
                'chat/index',
                'chat/getmessages',
                'chat/getusers',
                'chat/sendmessage',
                'chat/setheight',
                'chat/updateonline',
                'chat/togglecollapse',
                'chat/debugtime',
                'chat/testmessages',
                'chat/debugusers',
            ];
            
            // Проверяем, разрешен ли текущий маршрут
            $isAllowed = false;
            foreach ($allowedActions as $allowed) {
                if (strpos($route, $allowed) === 0) {
                    $isAllowed = true;
                    break;
                }
            }
            
            // Если персонаж в бою и текущий маршрут не разрешен - редиректим на бой
            if (!is_null($user->in_battle) && !$isAllowed) {
                return $this->redirect("/battle/battle");
            }
            
            if (!is_null($user->instance_id) && !$isAllowed) {
                return $this->redirect("/dungeon/index");
            }
        } else {
            // Перенаправляем только если это не публичное действие
            if (!in_array($action->id, $this->publicActions())) {
                return $this->redirect("/site/login");
            }
        }
        
        // Если всё хорошо, продолжаем выполнение
        return parent::beforeAction($action);
    }
}