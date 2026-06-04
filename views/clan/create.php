<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создание клана';
?>

<div class="clan-container">
    <div class="clan-header">
        <h1>Создание клана</h1>
        <p class="subtitle">Стоимость создания: 10 EKR</p>
    </div>

    <div class="create-form">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        
        <div class="form-group">
            <label>Название клана</label>
            <?= $form->field($model, 'name')->textInput(['class' => 'form-input', 'placeholder' => 'Введите название'])->label(false) ?>
        </div>
        
        <div class="form-group">
            <label>Иконка клана (20x20 пикселей)</label>
            <?= $form->field($model, 'imageFile')->fileInput(['class' => 'form-file', 'accept' => 'image/*'])->label(false) ?>
            <p class="hint">Рекомендуемый размер: 20x20 пикселей. Поддерживаются: PNG, JPG, GIF</p>
        </div>
        
        <button type="submit" class="btn-create">Создать клан</button>
        
        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
.clan-container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
}

.clan-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.clan-header h1 {
    color: #ffd700;
    font-size: 28px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
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
}

.form-input {
    width: 100%;
    padding: 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
    font-size: 16px;
}

.form-file {
    width: 100%;
    padding: 10px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #c9a87b;
}

.hint {
    font-size: 12px;
    color: #8b7355;
    margin-top: 5px;
}

.btn-create {
    width: 100%;
    background: #8b5e3c;
    color: #ffd700;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
}

.btn-create:hover {
    background: #a0744f;
}
</style>