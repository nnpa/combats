<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\models\Clan;
use app\models\ClanUser;
use app\models\User;

class ClanController extends Controller
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

    // Главная страница кланов
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);
        
        // Проверяем, состоит ли пользователь в клане
        if ($user->clan_id) {
            $clan = Clan::findOne($user->clan_id);
            $members = ClanUser::find()
                ->where(['clan_id' => $clan->id, 'status' => 1])
                ->with('user')
                ->all();
            $requests = ClanUser::find()
                ->where(['clan_id' => $clan->id, 'status' => 0])
                ->with('user')
                ->all();
            $isLeader = ($clan->admin_id == $userId);
            
            return $this->render('view', [
                'clan' => $clan,
                'members' => $members,
                'requests' => $requests,
                'isLeader' => $isLeader,
            ]);
        } else {
            // Поиск кланов для вступления
            $clans = Clan::find()->all();
            return $this->render('join', [
                'clans' => $clans,
            ]);
        }
    }

public function actionCreate()
{
    $userId = Yii::$app->user->id;
    $user = User::findOne($userId);
    
    if ($user->clan_id) {
        Yii::$app->session->setFlash('error', 'Вы уже состоите в клане!');
        return $this->redirect(['index']);
    }
    
    if ($user->ekr < 10) {
        Yii::$app->session->setFlash('error', 'Недостаточно EKR! Нужно 10 EKR для создания клана.');
        return $this->redirect(['index']);
    }
    
    $model = new Clan();
    
    if ($model->load(Yii::$app->request->post())) {
        
        // Создаем папку для загрузок
        $uploadPath = Yii::getAlias('@webroot') . '/uploads/clan/';
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        // Обработка загруженного файла
        $imageFile = UploadedFile::getInstance($model, 'imageFile');
        
        if ($imageFile && $imageFile->tempName && file_exists($imageFile->tempName)) {
            // Генерируем уникальное имя
            $filename = 'clan_' . time() . '_' . rand(1000, 9999) . '.' . $imageFile->extension;
            $path = $uploadPath . $filename;
            
            // Сохраняем файл
            if ($imageFile->saveAs($path)) {
                $model->img = '/uploads/clan/' . $filename;
            } else {
                $model->img = '/img/clan/default.png';
            }
        } else {
            $model->img = '/img/clan/default.png';
        }
        
        // Списываем EKR
        $user->ekr -= 10;
        $user->save(false);
        
        $model->admin_id = $userId;
        
        if ($model->save()) {
            // Добавляем создателя в участники
            $clanUser = new ClanUser();
            $clanUser->clan_id = $model->id;
            $clanUser->user_id = $userId;
            $clanUser->status = 1;
            $clanUser->created_at = time();
            $clanUser->description = 'Глава клана';
            $clanUser->save();
            
            $user->clan_id = $model->id;
            $user->save(false);
            
            Yii::$app->session->setFlash('success', 'Клан "' . $model->name . '" успешно создан!');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при создании клана!');
        }
    }
    
    return $this->render('create', [
        'model' => $model,
    ]);
}

    // Подача заявки в клан
    public function actionApply()
    {
        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);
        
        // Проверяем, не в клане ли уже
        if ($user->clan_id) {
            Yii::$app->session->setFlash('error', 'Вы уже состоите в клане!');
            return $this->redirect(['index']);
        }
        
        $clanName = Yii::$app->request->post('clan_name');
        $clan = Clan::findOne(['name' => $clanName]);
        
        if (!$clan) {
            Yii::$app->session->setFlash('error', 'Клан не найден!');
            return $this->redirect(['index']);
        }
        
        // Проверяем, нет ли уже заявки
        $existing = ClanUser::findOne(['clan_id' => $clan->id, 'user_id' => $userId]);
        if ($existing) {
            Yii::$app->session->setFlash('error', 'Вы уже подали заявку в этот клан!');
            return $this->redirect(['index']);
        }
        
        $clanUser = new ClanUser();
        $clanUser->clan_id = $clan->id;
        $clanUser->user_id = $userId;
        $clanUser->status = 0;
        $clanUser->description = '';
        $clanUser->created_at = time();
        
        if ($clanUser->save()) {
            Yii::$app->session->setFlash('success', 'Заявка в клан "' . $clan->name . '" отправлена!');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при отправке заявки!');
        }
        
        return $this->redirect(['index']);
    }

    // Отмена заявки
    public function actionCancelRequest($id)
    {
        $userId = Yii::$app->user->id;
        $request = ClanUser::findOne(['id' => $id, 'user_id' => $userId, 'status' => 0]);
        
        if ($request) {
            $request->delete();
            Yii::$app->session->setFlash('success', 'Заявка отменена!');
        } else {
            Yii::$app->session->setFlash('error', 'Заявка не найдена!');
        }
        
        return $this->redirect(['index']);
    }

    // Принять заявку (только для главы)
    public function actionAcceptRequest($id)
    {
        $userId = Yii::$app->user->id;
        $request = ClanUser::findOne($id);
        
        if (!$request) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена!');
            return $this->redirect(['index']);
        }
        
        $clan = Clan::findOne($request->clan_id);
        
        if ($clan->admin_id != $userId) {
            Yii::$app->session->setFlash('error', 'У вас нет прав!');
            return $this->redirect(['index']);
        }
        
        $request->status = 1;
        
        if ($request->save()) {
            // Обновляем пользователя
            $user = User::findOne($request->user_id);
            $user->clan_id = $clan->id;
            $user->save(false);
            
            Yii::$app->session->setFlash('success', 'Игрок принят в клан!');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при принятии заявки!');
        }
        
        return $this->redirect(['index']);
    }

    // Отклонить заявку
    public function actionRejectRequest($id)
    {
        $userId = Yii::$app->user->id;
        $request = ClanUser::findOne($id);
        
        if (!$request) {
            Yii::$app->session->setFlash('error', 'Заявка не найдена!');
            return $this->redirect(['index']);
        }
        
        $clan = Clan::findOne($request->clan_id);
        
        if ($clan->admin_id != $userId) {
            Yii::$app->session->setFlash('error', 'У вас нет прав!');
            return $this->redirect(['index']);
        }
        
        $request->delete();
        Yii::$app->session->setFlash('success', 'Заявка отклонена!');
        
        return $this->redirect(['index']);
    }

    // Исключить участника
    public function actionKickMember($id)
    {
        $userId = Yii::$app->user->id;
        $member = ClanUser::findOne($id);
        
        if (!$member) {
            Yii::$app->session->setFlash('error', 'Участник не найден!');
            return $this->redirect(['index']);
        }
        
        $clan = Clan::findOne($member->clan_id);
        
        if ($clan->admin_id != $userId) {
            Yii::$app->session->setFlash('error', 'У вас нет прав!');
            return $this->redirect(['index']);
        }
        
        if ($member->user_id == $userId) {
            Yii::$app->session->setFlash('error', 'Нельзя исключить самого себя!');
            return $this->redirect(['index']);
        }
        
        // Обновляем пользователя
        $user = User::findOne($member->user_id);
        $user->clan_id = null;
        $user->save(false);
        
        $member->delete();
        Yii::$app->session->setFlash('success', 'Участник исключен из клана!');
        
        return $this->redirect(['index']);
    }

    // Обновить описание участника
public function actionUpdateDescription()
{
    $request = Yii::$app->request;
    
    // Получаем ID из POST
    $id = $request->post('id');
    $description = $request->post('description');
    
    if (!$id) {
        Yii::$app->session->setFlash('error', 'Не указан ID участника');
        return $this->redirect(['index']);
    }
    
    $userId = Yii::$app->user->id;
    $member = ClanUser::findOne($id);
    
    if (!$member) {
        Yii::$app->session->setFlash('error', 'Участник не найден!');
        return $this->redirect(['index']);
    }
    
    $clan = Clan::findOne($member->clan_id);
    
    if ($clan->admin_id != $userId) {
        Yii::$app->session->setFlash('error', 'У вас нет прав!');
        return $this->redirect(['index']);
    }
    
    $member->description = $description;
    
    if ($member->save()) {
        Yii::$app->session->setFlash('success', 'Описание обновлено!');
    } else {
        Yii::$app->session->setFlash('error', 'Ошибка при обновлении!');
    }
    
    return $this->redirect(['index']);
}

    // Покинуть клан
    public function actionLeave()
    {
        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);
        
        if (!$user->clan_id) {
            Yii::$app->session->setFlash('error', 'Вы не состоите в клане!');
            return $this->redirect(['index']);
        }
        
        $clan = Clan::findOne($user->clan_id);
        
        if ($clan->admin_id == $userId) {
            Yii::$app->session->setFlash('error', 'Глава клана не может покинуть клан! Сначала передайте лидерство или распустите клан.');
            return $this->redirect(['index']);
        }
        
        $member = ClanUser::findOne(['clan_id' => $user->clan_id, 'user_id' => $userId]);
        
        if ($member) {
            $member->delete();
        }
        
        $user->clan_id = null;
        $user->save(false);
        
        Yii::$app->session->setFlash('success', 'Вы покинули клан!');
        return $this->redirect(['index']);
    }

    // Распустить клан (только для главы)
    public function actionDisband()
    {
        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);
        
        if (!$user->clan_id) {
            Yii::$app->session->setFlash('error', 'Вы не состоите в клане!');
            return $this->redirect(['index']);
        }
        
        $clan = Clan::findOne($user->clan_id);
        
        if ($clan->admin_id != $userId) {
            Yii::$app->session->setFlash('error', 'У вас нет прав!');
            return $this->redirect(['index']);
        }
        
        // Удаляем всех участников
        $members = ClanUser::findAll(['clan_id' => $clan->id]);
        foreach ($members as $member) {
            $u = User::findOne($member->user_id);
            if ($u) {
                $u->clan_id = null;
                $u->save(false);
            }
            $member->delete();
        }
        
        // Удаляем клан
        $clan->delete();
        
        Yii::$app->session->setFlash('success', 'Клан распущен!');
        return $this->redirect(['index']);
    }
}