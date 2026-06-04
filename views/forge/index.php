<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Кузница';
?>

<div class="forge-container">
    <div class="forge-header">
        <h1>Кузница</h1>
        <p class="subtitle">Заточка оружия и гравировка предметов</p>
    </div>

    <div class="forge-layout">
        <!-- Левая колонка - Заточка предмета -->
        <div class="forge-sharpen-section">
            <div class="section-title">
                <h2>Заточка предмета</h2>
                <p class="section-desc">Увеличивает урон и защиту предмета на +2 за уровень</p>
            </div>

            <?php if (!$hasSharpening): ?>
                <div class="alert-warning">
                    <p>⚠️ У вас нет заклинания заточки!</p>
                    <p class="hint">Заклинание заточки можно получить в игре</p>
                </div>
            <?php elseif (empty($items)): ?>
                <div class="alert-info">
                    <p>Нет доступных предметов для заточки</p>
                    <p class="hint">Предмет должен быть в инвентаре, не надет и не отправлен по почте</p>
                </div>
            <?php else: ?>
                <div class="items-list">
                    <div class="items-header">
                        <div class="col-item">Предмет</div>
                        <div class="col-enchant">Заточка</div>
                        <div class="col-action"></div>
                    </div>
                    <?php foreach ($items as $item): ?>
                        <div class="item-row">
                            <div class="col-item">
                                <div class="item-info">
                                    <img src="/<?= $item->img ?>" alt="<?= Html::encode($item->name) ?>" class="item-img">
                                    <div class="item-details">
                                        <div class="item-name"><?= Html::encode($item->name) ?></div>
                                        <div class="item-type"><?= Html::encode($item->type) ?> | Уровень: <?= $item->n_level ?></div>
                                        <?php if($item->damage > 0): ?>
                                            <div class="item-stat">Урон: <?= $item->damage ?></div>
                                        <?php endif; ?>
                                        <?php if($item->defence > 0): ?>
                                            <div class="item-stat">Защита: <?= $item->defence ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-enchant">
                                <?php if ($item->enchant > 0): ?>
                                    <span class="enchant-badge">+<?= $item->enchant ?></span>
                                <?php else: ?>
                                    <span class="enchant-none">нет</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-action">
                                <?php if ($item->enchant >= 5): ?>
                                    <span class="max-enchant">Максимум</span>
                                <?php else: ?>
                                    <a href="/forge/sharpen?id=<?= $item->id ?>" class="btn-sharpen" onclick="return confirm('Заточить предмет «<?= Html::encode($item->name) ?>»? Урон и защита увеличатся на 2.')">Заточить +1</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Правая колонка - Гравировка предмета -->
        <div class="forge-engrave-section">
            <div class="section-title">
                <h2>Гравировка предмета</h2>
                <p class="section-desc">Нанесите уникальную надпись на предмет (до 20 символов)</p>
                <p class="section-price">Стоимость: 20 KR</p>
            </div>

            <?php if (empty($items)): ?>
                <div class="alert-info">
                    <p>Нет доступных предметов для гравировки</p>
                </div>
            <?php else: ?>
                <?php $form = ActiveForm::begin(['action' => ['engrave'], 'method' => 'post']); ?>
                
                <div class="form-group">
                    <label>Выберите предмет</label>
                    <select name="item_id" class="form-select" required>
                        <option value="">-- Выберите предмет --</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= $item->id ?>">
                                <?= Html::encode($item->name) ?> (<?= Html::encode($item->type) ?>)
                                <?php if ($item->enchant > 0): ?> [+<?= $item->enchant ?>]<?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Текст гравировки (до 20 символов)</label>
                    <input type="text" name="engraving_text" class="form-input" maxlength="20" placeholder="Введите текст гравировки..." required>
                    <p class="hint">Гравировка появится в описании предмета</p>
                </div>
                
                <div class="user-balance">
                    <span class="balance-label">Ваш баланс KR:</span>
                    <span class="balance-value"><?= number_format($user->kr, 0, ',', ' ') ?></span>
                </div>
                
                <button type="submit" class="btn-engrave">Нанести гравировку (20 KR)</button>
                
                <?php ActiveForm::end(); ?>
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

.forge-container {
    width: 100%;
    max-width: 100%;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
}

.forge-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.forge-header h1 {
    color: #ffd700;
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
}

.forge-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

@media (max-width: 900px) {
    .forge-layout {
        grid-template-columns: 1fr;
    }
}

.forge-sharpen-section, .forge-engrave-section {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #5c3a2a;
}

.section-title {
    margin-bottom: 20px;
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

.section-price {
    color: #ffd700;
    font-size: 16px;
    font-weight: bold;
    margin-top: 5px;
}

.alert-warning, .alert-info {
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.alert-warning {
    background: #6b2d2d;
    color: #ffaa66;
}

.alert-info {
    background: #2c1810;
    color: #c9a87b;
}

.hint {
    font-size: 12px;
    margin-top: 5px;
    color: #8b7355;
}

/* Список предметов */
.items-list {
    max-height: 500px;
    overflow-y: auto;
}

.items-header {
    display: grid;
    grid-template-columns: 3fr 0.8fr 1.2fr;
    padding: 12px;
    background: #2c1810;
    border-radius: 8px;
    margin-bottom: 10px;
    color: #c9a87b;
    font-size: 13px;
    font-weight: bold;
}

.item-row {
    display: grid;
    grid-template-columns: 3fr 0.8fr 1.2fr;
    padding: 12px;
    background: #2c1810;
    border-radius: 8px;
    margin-bottom: 8px;
    align-items: center;
    transition: background 0.2s;
}

.item-row:hover {
    background: #4a2e1f;
}

.item-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.item-img {
    width: 40px;
    height: 40px;
    object-fit: contain;
}

.item-name {
    color: #ffd700;
    font-weight: bold;
    font-size: 14px;
}

.item-type {
    color: #c9a87b;
    font-size: 11px;
}

.item-stat {
    color: #66ff66;
    font-size: 11px;
}

.col-enchant {
    text-align: center;
}

.enchant-badge {
    display: inline-block;
    padding: 4px 8px;
    background: #8b5e3c;
    color: #ffd700;
    border-radius: 4px;
    font-weight: bold;
    font-size: 12px;
}

.enchant-none {
    color: #8b7355;
    font-size: 12px;
}

.max-enchant {
    display: inline-block;
    padding: 4px 8px;
    background: #6b2d2d;
    color: #ff6666;
    border-radius: 4px;
    font-size: 12px;
}

.btn-sharpen {
    display: inline-block;
    padding: 6px 12px;
    background: #8b5e3c;
    color: #ffd700;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
}

.btn-sharpen:hover {
    background: #a0744f;
}

/* Форма гравировки */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #ffd700;
    margin-bottom: 8px;
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

.user-balance {
    background: #2c1810;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
}

.balance-label {
    color: #c9a87b;
    margin-right: 10px;
}

.balance-value {
    color: #ffd700;
    font-size: 18px;
    font-weight: bold;
}

.btn-engrave {
    width: 100%;
    padding: 12px;
    background: #8b5e3c;
    color: #ffd700;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
}

.btn-engrave:hover {
    background: #a0744f;
}

@media (max-width: 768px) {
    .items-header {
        display: none;
    }
    
    .item-row {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .col-enchant, .col-action {
        text-align: left;
    }
    
    .btn-sharpen {
        width: 100%;
        text-align: center;
    }
}
</style>