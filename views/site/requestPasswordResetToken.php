<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Сброс пароля';
?>

<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, укажите ваш email. Ссылка для сброса пароля будет отправлена на него.</p>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'type' => 'email']) ?>

        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>