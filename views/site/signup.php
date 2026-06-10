<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация';
?>

<div class="site-signup">
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h1>🏰 Dungeon RPG</h1>
                <p class="subtitle">Создание нового аккаунта</p>
            </div>

            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <div class="signup-form">
                <?= $form->field($model, 'username')->textInput([
                    'autofocus' => true,
                    'placeholder' => 'Введите имя пользователя',
                    'class' => 'signup-input'
                ])->label('Имя пользователя') ?>

                <?= $form->field($model, 'email')->textInput([
                    'placeholder' => 'Введите email',
                    'class' => 'signup-input',
                    'type' => 'email'
                ])->label('Email') ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => 'Введите пароль',
                    'class' => 'signup-input'
                ])->label('Пароль') ?>

                <div class="signup-buttons">
                    <?= Html::submitButton('📝 Зарегистрироваться', ['class' => 'btn-signup', 'name' => 'signup-button']) ?>
                </div>
                <div class="signup-agreement">
                    <?= Html::a('Вы автоматически соглашаетесь с пользовательским соглашением', ['/site/doc']) ?>
                </div>
                <div class="signup-links">
                    <?= Html::a('Уже есть аккаунт? Войти', ['site/login']) ?>
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

.site-signup {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a0f0a 0%, #0d0805 100%);
    padding: 20px;
}

.signup-container {
    width: 100%;
    max-width: 450px;
}

.signup-card {
    background: linear-gradient(145deg, #2b1d12 0%, #1f150c 100%);
    border: 2px solid #7b5a2f;
    border-radius: 24px;
    padding: 35px 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,215,120,0.1);
}

.signup-header {
    text-align: center;
    margin-bottom: 30px;
}

.signup-header h1 {
    color: #ffd700;
    font-size: 28px;
    margin-bottom: 8px;
    text-shadow: 2px 2px 0 #2a1a0a;
}

.subtitle {
    color: #c9a87b;
    font-size: 14px;
}

.signup-form {
    margin-bottom: 20px;
}

.signup-form .form-group {
    margin-bottom: 20px;
}

.signup-form label {
    display: block;
    color: #ffd700;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: bold;
    letter-spacing: 0.5px;
}

.signup-input {
    width: 100%;
    padding: 12px 15px;
    background: #1a0f0a;
    border: 1px solid #5c3a2a;
    border-radius: 10px;
    color: #ffd700;
    font-size: 15px;
    transition: all 0.3s;
}

.signup-input:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 10px rgba(255,215,0,0.2);
}

.signup-input::placeholder {
    color: #5c3a2a;
}

.help-block {
    color: #ff6666;
    font-size: 12px;
    margin-top: 5px;
}

.signup-buttons {
    margin: 25px 0 20px;
}

.btn-signup {
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

.btn-signup:hover {
    background: linear-gradient(135deg, #a0744f, #7a4f2f);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.signup-links {
    text-align: center;
}

.signup-links a {
    color: #c9a87b;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
}

.signup-links a:hover {
    color: #ffd700;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .signup-card {
        padding: 25px 20px;
    }
    
    .signup-header h1 {
        font-size: 24px;
    }
    
    .btn-signup {
        font-size: 16px;
        padding: 12px;
    }
}
</style>