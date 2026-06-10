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
            'site/index',
            'site/login',
            'site/signup',
            'site/requestpasswordreset',
            'site/reset-password',
            'site/verify-email',
            'site/error',
            'site/captcha',
            'site/logout',
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
                        'actions' => array_map(function($action) {
                            return str_replace('site/', '', $action);
                        }, $this->publicActions()),
                        'roles' => ['?', '@'],
                    ],
                    // Все остальные действия только для авторизованных
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if ($action->id !== 'logout') {
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
        
        // Получаем полный путь контроллер/действие
        $fullRoute = $action->controller->id . '/' . $action->id;
        
        // Список публичных маршрутов (полные пути)
        $publicRoutes = $this->publicActions();
        
        // Если это публичный маршрут - сразу пропускаем
        if (in_array($fullRoute, $publicRoutes)) {
            return parent::beforeAction($action);
        }
        
        $user = Yii::$app->user->identity;
        
        // Если пользователь не авторизован - редирект на логин
        if (is_null($user)) {
            // Важно: не делаем редирект, если уже на странице логина
            if ($fullRoute !== 'site/login') {
                return $this->redirect(['/site/login']);
            }
            return parent::beforeAction($action);
        }
        
        // Для авторизованных пользователей
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
            "dungeon/loadplayer",
            "dungeon/loadmap",
            "dungeon/loadmonsters",
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
        
        // Если всё хорошо, продолжаем выполнение
        return parent::beforeAction($action);
    }
}