<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\User;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\SignupForm;
use app\models\Battle;
use app\models\UserBattle;
use app\models\EventBoss;
use app\models\EventParticipants;
use app\models\Chat;
use app\models\ResetPasswordForm;
use app\models\PasswordResetRequestForm;
use app\models\Item;
use yii\helpers\FileHelper;

class SiteController extends AppController
{
    public function actionFolder(){
        // Все уникальные пары (класс, уровень)
$items = Item::find()
    ->select(['class', 'n_level'])
    ->where(['is', 'class', null])->orWhere(['!=', 'class', '']) // исключаем NULL и пустые строки
    ->distinct()
    ->all();

$basePath = Yii::getAlias('@webroot') . '/generated_items/';

foreach ($items as $item) {
    if (empty($item->class) || empty($item->n_level)) continue;

    // Формируем имя папки: например "Критовик_2ур"
    $folderName = $item->class . '_' . $item->n_level . 'ур';
    $fullPath = $basePath . $folderName;

    if (!is_dir($fullPath)) {
        FileHelper::createDirectory($fullPath, 0777);
        echo "✅ Создана папка: {$folderName}\n";
    } else {
        echo "⚠️ Папка уже существует: {$folderName}\n";
    }
}

echo "\n🎉 Готово! Все папки созданы.\n";
    }
    /**
     * Настройка правил доступа (Access Control).
     * Гость (неавторизованный) может видеть страницу входа.
     * Авторизованный может выйти.
     */
    
    public function actionInfo($username){
        $this->layout = false;
        
        $user = User::findOne(["username"=>$username]);
        if(!is_null($user)){
            return $this->render("info",["user"=>$user]);

        }
    }
    
 

    /**
     * Объявление действий, например, для страницы ошибок.
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
private function getActiveEvent()
{
    $now = date('Y-m-d H:i:s');
    
    // Отладка
    Yii::info("Checking active event at: {$now}", 'event');
    
    $event = EventBoss::find()
        ->where(['is_active' => 1])
        ->andWhere(['<=', 'start_time', $now])
        ->andWhere(['>=', 'end_time', $now])
        ->one();
    
    if (!$event) {
        Yii::info("No active event found", 'event');
        return null;
    }
    
    Yii::info("Active event found: ID={$event->id}, battle_id={$event->battle_id}", 'event');
    
    if ($event && $event->battle_id) {
        $bossBattle = UserBattle::find()
            ->where(['battle_id' => $event->battle_id, 'bot_id' => $event->bot_id])
            ->one();
        
        if (!$bossBattle) {
            Yii::info("Boss battle not found for event {$event->id}", 'event');
        } else {
            Yii::info("Boss HP: {$bossBattle->hp}", 'event');
        }
        
        if ($bossBattle && $bossBattle->hp <= 0) {
            $event->is_active = 0;
            $event->save(false);
            Yii::info("Event {$event->id} deactivated because boss is dead", 'event');
            return null;
        }
        
        if ($event->current_hp != $bossBattle->hp) {
            $event->current_hp = $bossBattle->hp;
            $event->save(false);
        }
    }
    
    return $event;
}
    /**
     * Действие для страницы входа.
     */
public function actionLogin()
{
    // Если пользователь уже вошел, перенаправляем его на CP
    if (!Yii::$app->user->isGuest) {
        return $this->redirect(['site/cp']);
    }

    $model = new LoginForm();

    // Если форма отправлена и прошла валидацию
    if ($model->load(Yii::$app->request->post()) && $model->login()) {
        // После успешного входа перенаправляем на CP
        return $this->redirect(['site/cp']);
    }

    // Отображаем вид с формой входа
    return $this->render('login', [
        'model' => $model,
    ]);
}

    /**
     * Действие для выхода из системы.
     */
public function actionLogout()
{
    $user = Yii::$app->user->identity;
    
    if ($user) {
        $user->session_id = null;
        $user->session_expire = null;
        $user->isOnline = null;
        $user->save(false);
    }
    
    Yii::$app->user->logout();
    
    // Перенаправляем на страницу входа, НЕ на Cp
    return $this->redirect(['login']);
}
    
     /**
     * Регистрация пользователя.
     */
    public function actionSignup()
    {
        // Если пользователь уже авторизован, не показываем форму регистрации
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            // Регистрация прошла успешно, можно показать сообщение и перенаправить на логин
            Yii::$app->session->setFlash('success', 'Регистрация прошла успешно! Теперь вы можете войти.');
            return $this->redirect(['login']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
    
   public function actionRequestpasswordreset()
    {
        $model = new PasswordResetRequestForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для дальнейших инструкций.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Извините, мы не можем сбросить пароль для указанного email.');
            }
        }
        
        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }
    
    /**
     * Сброс пароля.
     */
    public function actionResetPassword($token)
    {
         try {
             $model = new ResetPasswordForm($token);
         } catch (InvalidArgumentException $e) {
             Yii::$app->session->setFlash('error', $e->getMessage());
             return $this->redirect(['request-password-reset']);
         }
         
         if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
             Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');
             return $this->redirect(['login']);
         }
         
         return $this->render('resetPassword', [
             'model' => $model,
         ]);
     }
     
     
     public function actionCp()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }
    
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    $activeEvent = $this->getActiveEvent();
    
    $isParticipant = false;
    $inBattle = false;
    $currentBattle = null;
    
    // ВАЖНО: проверяем $activeEvent перед использованием
    if ($activeEvent && $user) {
        $participant = EventParticipants::find()
            ->where(['event_id' => $activeEvent->id, 'user_id' => $user->id])
            ->one();
        
        if ($participant) {
            $isParticipant = true;
            $inBattle = true;
        }
        
        if ($activeEvent->battle_id) {
            $currentBattle = Battle::findOne(['id' => $activeEvent->battle_id]);
            if ($currentBattle && $currentBattle->started == 2) {
                $inBattle = false;
            }
        }
    }
    
    return $this->render('cp', [
        'user' => $user,
        'activeEvent' => $activeEvent,
        'isParticipant' => $isParticipant,
        'inBattle' => $inBattle,
        'currentBattle' => $currentBattle,
    ]);
}
    public function actionStr(){
         return $this->render("str");
     }

     public function actionIndex(){
         return $this->render("index");
     }
}
?>