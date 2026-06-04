<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Кланы';
?>

<div class="clan-container">
    <div class="clan-header">
        <h1>Кланы</h1>
        <p class="subtitle">Создайте свой клан или вступите в существующий</p>
    </div>

    <div class="clan-layout">
        <!-- Создание клана -->
        <div class="clan-create-section">
            <div class="section-title">
                <h2>Создать клан</h2>
            </div>
            
            <div class="create-info">
                <p>Стоимость создания: <strong>10 EKR</strong></p>
                <p>Вы станете главой клана и сможете управлять участниками</p>
            </div>
            
            <?php $form = ActiveForm::begin(['action' => ['create'], 'options' => ['enctype' => 'multipart/form-data']]); ?>
            
            <div class="form-group">
                <label>Название клана</label>
                <?= Html::textInput('Clan[name]', '', ['class' => 'form-input', 'required' => true]) ?>
            </div>
            
            <div class="form-group">
                <label>Иконка клана (20x20 пикселей)</label>
                <?= Html::fileInput('Clan[imageFile]', '', ['class' => 'form-file', 'accept' => 'image/*']) ?>
                <p class="hint">Рекомендуемый размер: 20x20 пикселей</p>
            </div>
            
            <button type="submit" class="btn-create">Создать клан</button>
            
            <?php ActiveForm::end(); ?>
        </div>

        <!-- Вступление в клан -->
        <div class="clan-join-section">
            <div class="section-title">
                <h2>Вступить в клан</h2>
            </div>
            
            <?php if (empty($clans)): ?>
                <div class="empty-message">Нет доступных кланов для вступления</div>
            <?php else: ?>
                <div class="clans-list">
                    <?php foreach ($clans as $clan): ?>
                        <div class="clan-card">
                            <div class="clan-icon">
                                <img src="<?= $clan->img ?>" alt="<?= Html::encode($clan->name) ?>">
                            </div>
                            <div class="clan-info">
                                <div class="clan-name"><?= Html::encode($clan->name) ?></div>
                                <div class="clan-master">Глава: <?= Html::encode($clan->admin->username ?? 'Неизвестен') ?></div>
                            </div>
                            <div class="clan-action">
                                <form action="/clan/apply" method="post">
                                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                                    <input type="hidden" name="clan_name" value="<?= Html::encode($clan->name) ?>">
                                    <button type="submit" class="btn-apply">Подать заявку</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.clan-container {
    width: 100%;
    max-width: 100%;
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
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
}

.clan-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

@media (max-width: 900px) {
    .clan-layout {
        grid-template-columns: 1fr;
    }
}

.clan-create-section, .clan-join-section {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #5c3a2a;
}

.section-title h2 {
    color: #ffd700;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #5c3a2a;
}

.create-info {
    background: #2c1810;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.create-info p {
    color: #c9a87b;
    margin: 5px 0;
}

.create-info strong {
    color: #ffd700;
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

.clans-list {
    max-height: 500px;
    overflow-y: auto;
}

.clan-card {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #2c1810;
    border-radius: 10px;
    margin-bottom: 12px;
}

.clan-card:hover {
    background: #4a2e1f;
}

.clan-icon {
    width: 50px;
    height: 50px;
    margin-right: 15px;
}

.clan-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.clan-info {
    flex: 1;
}

.clan-name {
    color: #ffd700;
    font-weight: bold;
    font-size: 16px;
}

.clan-master {
    color: #c9a87b;
    font-size: 12px;
}

.btn-apply {
    background: #2d6b2d;
    color: #66ff66;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.btn-apply:hover {
    background: #3d8b3d;
}

.empty-message {
    text-align: center;
    padding: 40px;
    color: #c9a87b;
}
</style>