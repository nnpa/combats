<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Вход в аккаунт';
?>

<div class="site-login">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>🏰 Dungeon RPG</h1>
                <p class="subtitle">Вход в аккаунт</p>
            </div>

            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <div class="login-form">
                <?= $form->field($model, 'username')->textInput([
                    'autofocus' => true,
                    'placeholder' => 'Введите имя пользователя',
                    'class' => 'login-input'
                ])->label('Имя пользователя') ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => 'Введите пароль',
                    'class' => 'login-input'
                ])->label('Пароль') ?>

                <div class="checkbox-group">
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "{input} {label}",
                        'class' => 'login-checkbox'
                    ]) ?>
                </div>

                <div class="login-buttons">
                    <?= Html::submitButton('🎮 Войти', ['class' => 'btn-login', 'name' => 'login-button']) ?>
                </div>

                <div class="login-links">
                    <?= Html::a('Забыли пароль?', ['site/request-password-reset']) ?>
                    <?= Html::a('Регистрация', ['site/signup']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.site-login {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a0f0a 0%, #0d0805 100%);
    padding: 20px;
}

.login-container {
    width: 100%;
    max-width: 450px;
}

.login-card {
    background: linear-gradient(145deg, #2b1d12 0%, #1f150c 100%);
    border: 2px solid #7b5a2f;
    border-radius: 24px;
    padding: 35px 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,215,120,0.1);
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h1 {
    color: #ffd700;
    font-size: 28px;
    margin-bottom: 8px;
    text-shadow: 2px 2px 0 #2a1a0a;
}

.subtitle {
    color: #c9a87b;
    font-size: 14px;
}

.login-form {
    margin-bottom: 20px;
}

.login-form .form-group {
    margin-bottom: 20px;
}

.login-form label {
    display: block;
    color: #ffd700;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: bold;
    letter-spacing: 0.5px;
}

.login-input {
    width: 100%;
    padding: 12px 15px;
    background: #1a0f0a;
    border: 1px solid #5c3a2a;
    border-radius: 10px;
    color: #ffd700;
    font-size: 15px;
    transition: all 0.3s;
}

.login-input:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 10px rgba(255,215,0,0.2);
}

.login-input::placeholder {
    color: #5c3a2a;
}

.checkbox-group {
    margin: 20px 0;
}

.login-checkbox {
    margin-right: 8px;
    accent-color: #ffd700;
}

.checkbox-group label {
    color: #c9a87b;
    font-size: 13px;
}

.login-buttons {
    margin: 25px 0 20px;
}

.btn-login {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #8b5e3c, #5a3d1f);
    border: 1px solid #d2a45b;
    border-radius: 12px;
    color: #ffd700;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-login:hover {
    background: linear-gradient(135deg, #a0744f, #7a4f2f);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.login-links {
    text-align: center;
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}

.login-links a {
    color: #c9a87b;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
}

.login-links a:hover {
    color: #ffd700;
    text-decoration: underline;
}

/* Сообщения об ошибках */
.help-block {
    color: #ff6666;
    font-size: 12px;
    margin-top: 5px;
}

/* Адаптация для мобильных */
@media (max-width: 480px) {
    .login-card {
        padding: 25px 20px;
    }
    
    .login-header h1 {
        font-size: 24px;
    }
    
    .btn-login {
        font-size: 16px;
        padding: 12px;
    }
}
</style>