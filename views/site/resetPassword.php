<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Сброс пароля';
?>

<div class="reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, введите новый пароль:</p>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>