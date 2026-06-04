<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Продать предмет';
?>

<div class="auction-container">
    <div class="auction-header">
        <h1>Продажа предмета</h1>
        <p class="subtitle">Выставьте предмет на аукцион</p>
    </div>

    <div class="sell-section">
        <div class="section-title">
            <h2>Выставить на продажу</h2>
            <p class="section-desc">Выберите предмет из инвентаря и установите цену</p>
        </div>

        <?php if (empty($items)): ?>
            <div class="empty-message">
                <p>Нет доступных предметов для продажи</p>
                <p class="hint">Предметы должны быть не надеты, не отправлены по почте и не выставлены на продажу</p>
            </div>
        <?php else: ?>
            <?php $form = ActiveForm::begin(['method' => 'post']); ?>

            <div class="form-group">
                <label>Выберите предмет</label>
                <select name="item_id" class="form-select" required>
                    <option value="">-- Выберите предмет --</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= $item->id ?>">
                            <?= Html::encode($item->name) ?> (Ур. <?= $item->n_level ?>, <?= Html::encode($item->type) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Цена (KR)</label>
                <input type="number" name="price" class="form-input" placeholder="Введите цену" min="1" required>
                <p class="hint">Укажите цену, за которую вы хотите продать предмет</p>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-sell">Выставить на продажу</button>
                <a href="/auction/index" class="btn-back">Вернуться к аукциону</a>
            </div>

            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.auction-container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
}

.auction-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.auction-header h1 {
    color: #ffd700;
    font-size: 28px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
}

.sell-section {
    background: #3d2317;
    border-radius: 12px;
    padding: 25px;
    border: 1px solid #5c3a2a;
}

.section-title {
    margin-bottom: 25px;
    text-align: center;
}

.section-title h2 {
    color: #ffd700;
    margin-bottom: 5px;
}

.section-desc {
    color: #c9a87b;
    font-size: 13px;
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

.form-select, .form-input {
    width: 100%;
    padding: 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
    font-size: 14px;
}

.form-select:focus, .form-input:focus {
    outline: none;
    border-color: #ffd700;
}

.hint {
    font-size: 12px;
    color: #8b7355;
    margin-top: 5px;
}

.form-buttons {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.btn-sell, .btn-back {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
}

.btn-sell {
    background: #8b5e3c;
    color: #ffd700;
}

.btn-sell:hover {
    background: #a0744f;
}

.btn-back {
    background: #5c3a2a;
    color: #c9a87b;
}

.btn-back:hover {
    background: #7a4f3a;
    color: #ffd700;
}

.empty-message {
    text-align: center;
    padding: 40px;
    color: #c9a87b;
}

.empty-message .hint {
    margin-top: 10px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .auction-container {
        padding: 10px;
    }
    
    .form-buttons {
        flex-direction: column;
    }
}
</style>