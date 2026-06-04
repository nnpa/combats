<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;

$this->title = 'Аукцион';
?>

<div class="auction-container">
    <div class="auction-header">
        <h1>Аукцион</h1>
        <p class="subtitle">Покупка и продажа предметов</p>
    </div>

    <!-- Вкладки -->
    <div class="auction-tabs">
        <button class="tab-btn active" data-tab="buy">Купить</button>
        <button class="tab-btn" data-tab="sell">Продать</button>
        <button class="tab-btn" data-tab="my-lots">Мои лоты</button>
    </div>

    <!-- Вкладка 1: Купить -->
    <div id="tab-buy" class="tab-content active">
        <!-- Фильтры -->
        <div class="filter-panel">
            <form method="get" action="/auction/index" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Тип предмета</label>
                        <select name="type" class="filter-select">
                            <option value="all">Все типы</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= Html::encode($type) ?>" <?= $typeFilter == $type ? 'selected' : '' ?>>
                                    <?= Html::encode($type) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Уровень</label>
                        <select name="level" class="filter-select">
                            <option value="all">Все уровни</option>
                            <?php for($i = 1; $i <= 20; $i++): ?>
                                <option value="<?= $i ?>" <?= $levelFilter == $i ? 'selected' : '' ?>>
                                    <?= $i ?> уровень
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-group filter-buttons">
                        <label>&nbsp;</label>
                        <div class="button-group">
                            <button type="submit" class="btn-filter">Найти</button>
                            <a href="/auction/index" class="btn-reset">Сброс</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($lots)): ?>
            <div class="empty-message">На аукционе пока нет предметов</div>
        <?php else: ?>
            <div class="items-grid">
                <?php foreach ($lots as $lot): ?>
                    <?php $item = $lot->inventory; ?>
                    <?php if ($item): ?>
                        <div class="item-card">
                            <div class="item-image">
                                <img src="/<?= $item->img ?>" alt="<?= Html::encode($item->name) ?>">
                            </div>
                            <div class="item-content">
                                <div class="item-name"><?= Html::encode($item->name) ?></div>
                                
                                <div class="info-list">
                                    <div class="info-row">
                                        <span class="info-label">Уровень:</span>
                                        <span class="info-value"><?= $item->n_level ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Тип:</span>
                                        <span class="info-value"><?= Html::encode($item->type) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Продавец:</span>
                                        <span class="info-value"><?= Html::encode($lot->user->username ?? 'Неизвестен') ?></span>
                                    </div>
                                </div>
                                
                                <!-- Требования к ношению -->
                                <?php if($item->n_str > 0 || $item->n_dex > 0 || $item->n_end > 0 || $item->n_inte > 0 || $item->n_intu > 0 || $item->n_fire > 0 || $item->n_water > 0 || $item->n_air > 0 || $item->n_earth > 0): ?>
                                <div class="requirements-block">
                                    <div class="block-title">Требования</div>
                                    <div class="requirements-list">
                                        <?php if($item->n_level > 0): ?>
                                            <div class="req-row">Уровень: <?= $item->n_level ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_str > 0): ?>
                                            <div class="req-row">Сила: <?= $item->n_str ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_dex > 0): ?>
                                            <div class="req-row">Ловкость: <?= $item->n_dex ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_end > 0): ?>
                                            <div class="req-row">Выносливость: <?= $item->n_end ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_inte > 0): ?>
                                            <div class="req-row">Интеллект: <?= $item->n_inte ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_intu > 0): ?>
                                            <div class="req-row">Интуиция: <?= $item->n_intu ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_fire > 0): ?>
                                            <div class="req-row">Огонь: <?= $item->n_fire ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_water > 0): ?>
                                            <div class="req-row">Вода: <?= $item->n_water ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_air > 0): ?>
                                            <div class="req-row">Воздух: <?= $item->n_air ?></div>
                                        <?php endif; ?>
                                        <?php if($item->n_earth > 0): ?>
                                            <div class="req-row">Земля: <?= $item->n_earth ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Характеристики -->
                                <?php if($item->str > 0 || $item->dex > 0 || $item->end > 0 || $item->inte > 0 || $item->intu > 0 || $item->damage > 0 || $item->defence > 0 || $item->health > 0 || $item->mana > 0 || $item->crit > 0 || $item->anticrit > 0 || $item->mdef > 0 || $item->evaision > 0 || $item->aeveision > 0 || $item->fire > 0 || $item->water > 0 || $item->air > 0 || $item->earth > 0): ?>
                                <div class="stats-block">
                                    <div class="block-title">Характеристики</div>
                                    <div class="stats-list">
                                        <?php if($item->damage > 0): ?>
                                            <div class="stat-row damage">Урон: +<?= $item->damage ?></div>
                                        <?php endif; ?>
                                        <?php if($item->defence > 0): ?>
                                            <div class="stat-row defence">Защита: +<?= $item->defence ?></div>
                                        <?php endif; ?>
                                        <?php if($item->health > 0): ?>
                                            <div class="stat-row health">Здоровье: +<?= $item->health ?></div>
                                        <?php endif; ?>
                                        <?php if($item->mana > 0): ?>
                                            <div class="stat-row mana">Мана: +<?= $item->mana ?></div>
                                        <?php endif; ?>
                                        <?php if($item->crit > 0): ?>
                                            <div class="stat-row crit">Крит: +<?= $item->crit ?></div>
                                        <?php endif; ?>
                                        <?php if($item->anticrit > 0): ?>
                                            <div class="stat-row anticrit">Антикрит: +<?= $item->anticrit ?></div>
                                        <?php endif; ?>
                                        <?php if($item->mdef > 0): ?>
                                            <div class="stat-row mdef">Маг. защита: +<?= $item->mdef ?></div>
                                        <?php endif; ?>
                                        <?php if($item->evaision > 0): ?>
                                            <div class="stat-row evaision">Уклонение: +<?= $item->evaision ?></div>
                                        <?php endif; ?>
                                        <?php if($item->aeveision > 0): ?>
                                            <div class="stat-row aeveision">Маг. уклонение: +<?= $item->aeveision ?></div>
                                        <?php endif; ?>
                                        <?php if($item->str > 0): ?>
                                            <div class="stat-row">Сила: +<?= $item->str ?></div>
                                        <?php endif; ?>
                                        <?php if($item->dex > 0): ?>
                                            <div class="stat-row">Ловкость: +<?= $item->dex ?></div>
                                        <?php endif; ?>
                                        <?php if($item->end > 0): ?>
                                            <div class="stat-row">Выносливость: +<?= $item->end ?></div>
                                        <?php endif; ?>
                                        <?php if($item->inte > 0): ?>
                                            <div class="stat-row">Интеллект: +<?= $item->inte ?></div>
                                        <?php endif; ?>
                                        <?php if($item->intu > 0): ?>
                                            <div class="stat-row">Интуиция: +<?= $item->intu ?></div>
                                        <?php endif; ?>
                                        <?php if($item->fire > 0): ?>
                                            <div class="stat-row fire">Огонь: +<?= $item->fire ?></div>
                                        <?php endif; ?>
                                        <?php if($item->water > 0): ?>
                                            <div class="stat-row water">Вода: +<?= $item->water ?></div>
                                        <?php endif; ?>
                                        <?php if($item->air > 0): ?>
                                            <div class="stat-row air">Воздух: +<?= $item->air ?></div>
                                        <?php endif; ?>
                                        <?php if($item->earth > 0): ?>
                                            <div class="stat-row earth">Земля: +<?= $item->earth ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="lot-price">
                                    <span class="price-label">Цена:</span>
                                    <span class="price-value"><?= number_format($lot->cost, 0, ',', ' ') ?> KR</span>
                                </div>
                                
                                <div class="lot-actions">
                                    <a href="/auction/buy?id=<?= $lot->id ?>" class="btn-buy" onclick="return confirm('Купить предмет за <?= number_format($lot->cost, 0, ',', ' ') ?> KR?')">Купить</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <div class="pagination-container">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => ['class' => 'pagination'],
                    'nextPageLabel' => '→',
                    'prevPageLabel' => '←',
                ]) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Вкладка 2: Продать -->
    <div id="tab-sell" class="tab-content">
        <div class="sell-section">
            <div class="section-title">
                <h2>Продать предмет</h2>
                <p class="section-desc">Выставьте предмет на аукцион. Другие игроки смогут его купить.</p>
            </div>
            
            <?php if (empty($sellItems)): ?>
                <div class="empty-message">
                    <p>Нет доступных предметов для продажи</p>
                    <p class="hint">Предметы должны быть не надеты, не отправлены по почте и не выставлены на продажу</p>
                </div>
            <?php else: ?>
                <form action="/auction/sell" method="post" class="sell-form">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                    
                    <div class="form-group">
                        <label>Выберите предмет</label>
                        <select name="item_id" class="form-select" required>
                            <option value="">-- Выберите предмет --</option>
                            <?php foreach ($sellItems as $item): ?>
                                <option value="<?= $item->id ?>">
                                    <?= Html::encode($item->name) ?> (Ур. <?= $item->n_level ?>, <?= Html::encode($item->type) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Цена (KR)</label>
                        <input type="number" name="price" class="form-input" placeholder="Введите цену" min="1" required>
                    </div>
                    
                    <button type="submit" class="btn-sell">Выставить на продажу</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Вкладка 3: Мои лоты -->
    <div id="tab-my-lots" class="tab-content">
        <div class="my-lots-section">
            <div class="section-title">
                <h2>Мои лоты</h2>
                <p class="section-desc">Предметы, выставленные вами на продажу</p>
            </div>
            
            <?php if (empty($myLots)): ?>
                <div class="empty-message">
                    <p>У вас нет выставленных лотов</p>
                    <a href="#" class="btn-sell-link" onclick="document.querySelector('[data-tab=\'sell\']').click(); return false;">Продать предмет</a>
                </div>
            <?php else: ?>
                <div class="items-grid">
                    <?php foreach ($myLots as $item): ?>
                        <div class="item-card">
                            <div class="item-image">
                                <img src="/<?= $item->img ?>" alt="<?= Html::encode($item->name) ?>">
                            </div>
                            <div class="item-content">
                                <div class="item-name"><?= Html::encode($item->name) ?></div>
                                
                                <div class="info-list">
                                    <div class="info-row">
                                        <span class="info-label">Уровень:</span>
                                        <span class="info-value"><?= $item->n_level ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Тип:</span>
                                        <span class="info-value"><?= Html::encode($item->type) ?></span>
                                    </div>
                                </div>
                                
                                <div class="lot-price">
                                    <span class="price-label">Цена:</span>
                                    <span class="price-value">
                                        <?php 
                                            $auction = $item->auction;
                                            echo $auction ? number_format($auction->cost, 0, ',', ' ') : '0';
                                        ?> KR
                                    </span>
                                </div>
                                
                                <div class="lot-actions">
                                    <a href="/auction/remove-lot?id=<?= $item->id ?>" class="btn-remove" onclick="return confirm('Снять предмет с продажи?')">Снять с продажи</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Переключение вкладок
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');
        
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        this.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
        
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    });
});

const urlParams = new URLSearchParams(window.location.search);
const tabParam = urlParams.get('tab');
if (tabParam && ['buy', 'sell', 'my-lots'].includes(tabParam)) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.getAttribute('data-tab') === tabParam) {
            btn.click();
        }
    });
}
</script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.auction-container {
    width: 100%;
    max-width: 100%;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
}

/* Вкладки */
.auction-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
    border-bottom: 2px solid #5c3a2a;
    padding-bottom: 10px;
}

.tab-btn {
    background: none;
    border: none;
    padding: 10px 25px;
    font-size: 16px;
    font-weight: bold;
    color: #c9a87b;
    cursor: pointer;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s;
}

.tab-btn:hover {
    color: #ffd700;
}

.tab-btn.active {
    color: #ffd700;
    background: #3d2317;
    border-bottom: 3px solid #ffd700;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Фильтры */
.filter-panel {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    border: 1px solid #5c3a2a;
}

.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.filter-group label {
    display: block;
    color: #ffd700;
    margin-bottom: 5px;
    font-size: 12px;
}

.filter-select {
    width: 100%;
    padding: 10px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
}

.filter-buttons {
    flex: 0.5;
    min-width: 150px;
}

.button-group {
    display: flex;
    gap: 10px;
}

.btn-filter, .btn-reset {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    text-align: center;
}

.btn-filter {
    background: #8b5e3c;
    color: #ffd700;
}

.btn-reset {
    background: #5c3a2a;
    color: #c9a87b;
}

/* Сетка предметов */
.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* Карточка предмета */
.item-card {
    background: #3d2317;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #5c3a2a;
    transition: transform 0.2s;
}

.item-card:hover {
    transform: translateY(-3px);
    border-color: #ffd700;
}

.item-image {
    text-align: center;
    padding: 15px;
    background: #2c1810;
    border-bottom: 1px solid #5c3a2a;
}

.item-image img {
    max-width: 80px;
    max-height: 80px;
}

.item-content {
    padding: 15px;
}

.item-name {
    font-size: 18px;
    font-weight: bold;
    color: #ffd700;
    text-align: center;
    margin-bottom: 12px;
}

/* Списки */
.info-list, .requirements-list, .stats-list {
    margin-bottom: 12px;
}

.info-row, .req-row, .stat-row {
    padding: 6px 10px;
    margin: 2px 0;
    background: #2c1810;
    border-radius: 4px;
    font-size: 13px;
}

.info-row {
    display: flex;
    justify-content: space-between;
}

.info-label {
    color: #c9a87b;
}

.info-value {
    color: #ffd700;
    font-weight: bold;
}

.req-row {
    color: #ffaa66;
}

.stat-row {
    color: #e6c8a0;
}

/* Цвета для характеристик */
.stat-row.damage { background: #3d2018; color: #ffaa66; }
.stat-row.defence { background: #1d3d3d; color: #66ccff; }
.stat-row.health { background: #1d3d1d; color: #66ff66; }
.stat-row.mana { background: #2d1d3d; color: #cc66ff; }
.stat-row.crit { background: #3d2d1d; color: #ffd700; }
.stat-row.anticrit { background: #3d2d2d; color: #ffaa66; }
.stat-row.mdef { background: #2d2d3d; color: #aa88ff; }
.stat-row.fire { background: #3d1d1d; color: #ff8866; }
.stat-row.water { background: #1d3d3d; color: #66ccff; }
.stat-row.air { background: #2d3d3d; color: #66ccff; }
.stat-row.earth { background: #2d3d1d; color: #88ff66; }

.requirements-block, .stats-block {
    margin-bottom: 12px;
}

.block-title {
    font-size: 12px;
    color: #c9a87b;
    margin-bottom: 8px;
    font-weight: bold;
    padding-bottom: 4px;
    border-bottom: 1px solid #5c3a2a;
}

.lot-price {
    background: #2c1810;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 12px;
}

.price-label {
    color: #c9a87b;
    font-size: 12px;
}

.price-value {
    color: #ffd700;
    font-size: 18px;
    font-weight: bold;
    margin-left: 8px;
}

.lot-actions {
    width: 100%;
}

.btn-buy, .btn-remove, .btn-sell {
    display: block;
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
}

.btn-buy {
    background: #8b5e3c;
    color: #ffd700;
}

.btn-buy:hover {
    background: #a0744f;
}

.btn-remove {
    background: #6b2d2d;
    color: #ff6666;
}

.btn-sell {
    background: #8b5e3c;
    color: #ffd700;
    width: auto;
    padding: 12px 25px;
}

.sell-section, .my-lots-section {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #5c3a2a;
}

.section-title {
    margin-bottom: 20px;
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

.empty-message {
    text-align: center;
    padding: 50px;
    background: #3d2317;
    border-radius: 12px;
    color: #c9a87b;
}

.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    list-style: none;
    gap: 5px;
}

.pagination li a, .pagination li span {
    display: block;
    padding: 8px 12px;
    background: #3d2317;
    border: 1px solid #5c3a2a;
    border-radius: 6px;
    color: #c9a87b;
    text-decoration: none;
}

.pagination .active span {
    background: #8b5e3c;
    color: #ffd700;
}

@media (max-width: 768px) {
    .items-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-row {
        flex-direction: column;
    }
    
    .button-group {
        width: 100%;
    }
    
    .btn-filter, .btn-reset {
        flex: 1;
        text-align: center;
    }
}
</style>