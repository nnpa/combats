<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\models\Avatar;

class SettingsController extends Controller
{
    /**
     * Настройки доступа (только для авторизованных)
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Только авторизованные
                    ],
                ],
            ],
        ];
    }

    /**
     * Страница выбора аватара
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        // Получаем все аватары из таблицы avatar
        $avatars = Avatar::find()->all();
        
        // Текущий аватар пользователя (поле ava)
        $currentAvatar = $user->ava;
        
        return $this->render('index', [
            'user' => $user,
            'avatars' => $avatars,
            'currentAvatar' => $currentAvatar,
        ]);
    }
    
    /**
     * Сохранение выбранного аватара
     */
    public function actionSaveavatar()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        $selectedAvatarId = Yii::$app->request->post('avatar');
        
        if ($selectedAvatarId) {
            // Находим аватар по ID
            $avatar = Avatar::findOne(['id' => $selectedAvatarId]);
            
            if ($avatar) {
                // Сохраняем путь к картинке в поле ava пользователя
                $user->ava = $avatar->img;
                
                if ($user->save(false)) {
                    Yii::$app->session->setFlash('success', 'Аватар успешно изменён!');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при сохранении аватара.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Выбранный аватар не найден.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Пожалуйста, выберите аватар.');
        }
        
        return $this->redirect(['/settings']);
    }
}