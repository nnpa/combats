<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создание темы';
?>

<div class="forum-container">
    <div class="forum-header">
        <h1>📝 Создание новой темы</h1>
    </div>

    <div class="create-form">
        <?php $form = ActiveForm::begin(['id' => 'create-topic-form']); ?>

        <div class="form-group">
            <?= $form->field($model, 'title')->textInput([
                'maxlength' => true, 
                'placeholder' => 'Введите заголовок темы',
                'class' => 'form-input'
            ]) ?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'content')->textarea([
                'rows' => 10,
                'placeholder' => 'Введите содержание темы...',
                'class' => 'form-textarea'
            ]) ?>
        </div>

        <div class="captcha-section">
            <div class="captcha-question">
                <span class="captcha-icon">🔒</span>
                <span class="captcha-text">Введите результат: <?= $captchaQuestion ?></span>
            </div>
            <div class="captcha-input-group">
                <input type="text" name="ForumTopics[captcha]" class="captcha-input" placeholder="Ваш ответ" autocomplete="off">
            </div>
        </div>

        <div class="form-buttons">
            <?= Html::submitButton('➕ Создать тему', ['class' => 'btn-submit']) ?>
            <?= Html::a('◀ Назад к форуму', ['index'], ['class' => 'btn-back']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.forum-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #2c1810;
    min-height: 100vh;
}

.forum-header {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    border: 1px solid #5c3a2a;
    text-align: center;
}

.forum-header h1 {
    color: #ffd700;
    font-size: 28px;
    margin: 0;
}

.create-form {
    background: #3d2317;
    border-radius: 12px;
    padding: 25px;
    border: 1px solid #5c3a2a;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #ffd700;
    margin-bottom: 8px;
    font-weight: bold;
}

.form-input {
    width: 100%;
    padding: 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
    font-size: 14px;
    transition: all 0.2s;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #ffd700;
}

.form-textarea {
    width: 100%;
    padding: 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
    font-size: 14px;
    resize: vertical;
    font-family: inherit;
}

.captcha-section {
    background: #2c1810;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #5c3a2a;
}

.captcha-question {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.captcha-icon {
    font-size: 18px;
}

.captcha-text {
    color: #ffd700;
    font-size: 16px;
    font-weight: bold;
}

.captcha-input {
    width: 200px;
    padding: 10px;
    background: #3d2317;
    border: 1px solid #5c3a2a;
    border-radius: 6px;
    color: #ffd700;
    font-size: 14px;
}

.form-buttons {
    display: flex;
    gap: 15px;
}

.btn-submit {
    background: #8b5e3c;
    color: #ffd700;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-submit:hover {
    background: #a0744f;
    transform: scale(1.02);
}

.btn-back {
    background: #5c3a2a;
    color: #c9a87b;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    text-align: center;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #7a4f3a;
    color: #ffd700;
}

@media (max-width: 768px) {
    .forum-container {
        padding: 10px;
    }
    
    .form-buttons {
        flex-direction: column;
    }
    
    .btn-submit, .btn-back {
        text-align: center;
    }
    
    .captcha-input {
        width: 100%;
    }
}
</style>