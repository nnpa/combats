<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Магазин';
?>

<div class="shop-container">
    <div class="shop-header">
        <h1>Торговая лавка</h1>
        <div class="user-stats">
            <div class="stat-card">
                <span class="stat-label">KR</span>
                <span class="stat-value"><?= number_format($user->kr, 0, ',', ' ') ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">EKR</span>
                <span class="stat-value"><?= number_format($user->ekr, 0, ',', ' ') ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-label">Репутация</span>
                <span class="stat-value"><?= number_format($user->repa, 0, ',', ' ') ?></span>
            </div>
        </div>
    </div>

    <!-- Форма фильтрации -->
    <div class="filter-panel">
        <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['shop/index'], 'options' => ['class' => 'filter-form']]); ?>
        <div class="filter-grid">
            <div class="filter-item">
                <label>ТИП</label>
                <select name="cat" class="filter-select">
                    <option value="">Все типы</option>
                    <option value="weapon" <?= $selectedCat == 'weapon' ? 'selected' : '' ?>>Оружие</option>
                    <option value="helm" <?= $selectedCat == 'helm' ? 'selected' : '' ?>>Шлем</option>
                    <option value="chest" <?= $selectedCat == 'chest' ? 'selected' : '' ?>>Доспех</option>
                    <option value="leg" <?= $selectedCat == 'leg' ? 'selected' : '' ?>>Поножи</option>
                    <option value="shild" <?= $selectedCat == 'shild' ? 'selected' : '' ?>>Щит</option>
                    <option value="gloves" <?= $selectedCat == 'gloves' ? 'selected' : '' ?>>Перчатки</option>
                    <option value="boots" <?= $selectedCat == 'boots' ? 'selected' : '' ?>>Сапоги</option>
                    <option value="belt" <?= $selectedCat == 'belt' ? 'selected' : '' ?>>Пояс</option>
                    <option value="brasers" <?= $selectedCat == 'brasers' ? 'selected' : '' ?>>Наручи</option>
                    <option value="amulet" <?= $selectedCat == 'amulet' ? 'selected' : '' ?>>Амулет</option>
                    <option value="earrings" <?= $selectedCat == 'earrings' ? 'selected' : '' ?>>Серьги</option>
                    <option value="ring" <?= $selectedCat == 'ring' ? 'selected' : '' ?>>Кольцо</option>
                    <option value="el" <?= $selectedCat == 'el' ? 'selected' : '' ?>>Эликсиры</option>
                </select>
            </div>
            <div class="filter-item">
                <label>УРОВЕНЬ</label>
                <select name="level" class="filter-select">
                    <option value="">Все уровни</option>
                    <?php for($i = 2; $i <= 10; $i++): ?>
                        <option value="<?= $i ?>" <?= $selectedLevel == $i ? 'selected' : '' ?>><?= $i ?> уровень</option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-item">
                <label>КЛАСС</label>
                <select name="class" class="filter-select">
                    <option value="">Все классы</option>
                    <option value="Критовик" <?= $selectedClass == 'Критовик' ? 'selected' : '' ?>>Критовик</option>
                    <option value="Уворотчик" <?= $selectedClass == 'Уворотчик' ? 'selected' : '' ?>>Уворотчик</option>
                    <option value="Танк" <?= $selectedClass == 'Танк' ? 'selected' : '' ?>>Танк</option>
                    <option value="Маг" <?= $selectedClass == 'Маг' ? 'selected' : '' ?>>Маг Огня</option>
                    <option value="Маг Земли" <?= $selectedClass == 'Маг Земли' ? 'selected' : '' ?>>Маг Земли</option>
                    <option value="Маг Воды" <?= $selectedClass == 'Маг Воды' ? 'selected' : '' ?>>Маг Воды</option>
                </select>
            </div>
            <div class="filter-item filter-buttons">
                <label>&nbsp;</label>
                <div class="button-group">
                    <?= Html::submitButton('Найти', ['class' => 'btn-filter']) ?>
                    <?= Html::a('Сброс', ['shop/index'], ['class' => 'btn-reset']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <!-- Эликсиры -->
    <?php if (!empty($elexir)): ?>
        <div class="items-grid">
            <?php foreach ($elexir as $item): ?>
                <div class="item-card">
                    <div class="item-image">
                        <img src="/<?= $item->img ?>" alt="<?= Html::encode($item->name) ?>">
                    </div>
                    <div class="item-content">
                        <div class="item-name"><?= Html::encode($item->name) ?></div>
                        <div class="item-type"><?= $item->type ?></div>
                        <div class="purchase-block">
                            <div class="purchase-price">
                                <div class="price-item kr">Цена: <?= number_format($item->cost, 0, ',', ' ') ?> KR</div>
                            </div>
                            <div class="purchase-button">
                                <?= Html::a('Купить', ['shop/buyelexir', 'id' => $item->id], ['class' => 'btn-buy btn-kr']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Обычные предметы -->
    <?php if (!empty($items)): ?>
        <div class="items-grid">
            <?php foreach ($items as $item): ?>
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
                                <span class="info-label">Класс:</span>
                                <span class="info-value"><?= Html::encode($item->class ?: 'Любой') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Тип:</span>
                                <span class="info-value"><?= $item->type ?></span>
                            </div>
                        </div>
                        
                        <!-- Требования к ношению -->
                        <?php if($item->n_str > 0 || $item->n_dex > 0 || $item->n_end > 0 || $item->n_inte > 0 || $item->n_intu > 0 || $item->n_fire > 0 || $item->n_water > 0 || $item->n_air > 0 || $item->n_earth > 0): ?>
                        <div class="requirements-block">
                            <div class="block-title">Требования</div>
                            <div class="requirements-list">
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
                        
                        <!-- Добавляемые статы -->
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
                        
                        <?php if($item->description): ?>
                            <div class="item-description"><?= Html::encode($item->description) ?></div>
                        <?php endif; ?>
                        
                        <div class="purchase-block">
                            <div class="purchase-price">
                                <?php if($item->cost > 0): ?>
                                    <div class="price-item kr">Цена: <?= number_format($item->cost, 0, ',', ' ') ?> KR</div>
                                <?php endif; ?>
                                <?php if($item->repa > 0): ?>
                                    <div class="price-item repa <?= $user->repa >= $item->repa ? 'enough' : 'not-enough' ?>">
                                        Репутация: <?= $item->repa ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($item->cost_ekr > 0): ?>
                                    <div class="price-item ekr <?= $user->ekr >= $item->cost_ekr ? 'enough' : 'not-enough' ?>">
                                        EKR: <?= number_format($item->cost_ekr, 0, ',', ' ') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="purchase-button">
                                <?php if($item->cost > 0 && $item->repa == 0 && $item->cost_ekr == 0): ?>
                                    <a class="btn-buy btn-kr" 
                                        href="/shop/buy?id=<?= $item->id ?>" 
                                        onclick="return confirm('Купить <?= Html::encode($item->name) ?> за <?= number_format($item->cost, 0, ',', ' ') ?> KR?');">
                                         Купить
                                     </a>
                                <?php elseif($item->cost_ekr > 0 && $user->ekr >= $item->cost_ekr): ?>
                                    <?= Html::a('Купить за EKR', ['shop/buyekr', 'id' => $item->id], ['class' => 'btn-buy btn-ekr']) ?>
                                <?php elseif($item->repa > 0 && $user->repa >= $item->repa && $user->kr >= $item->cost): ?>
                                    <?= Html::a('Купить', ['shop/buyrepa', 'id' => $item->id], ['class' => 'btn-buy btn-repa']) ?>
                                <?php else: ?>
                                    <button class="btn-buy btn-disabled" disabled>Недоступно</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="pagination">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination-list'],
                'nextPageLabel' => '>',
                'prevPageLabel' => '<',
            ]) ?>
        </div>
    <?php elseif (empty($elexir)): ?>
        <div class="empty-message">
            <p>В лавке пока нет товаров</p>
        </div>
    <?php endif; ?>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.shop-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #2c1810;
    min-height: 100vh;
}

/* Шапка */
.shop-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding: 15px 20px;
    background: #3d2317;
    border-radius: 8px;
    border: 1px solid #5c3a2a;
    flex-wrap: wrap;
    gap: 15px;
}

.shop-header h1 {
    color: #ffd700;
    font-size: 24px;
    margin: 0;
    text-shadow: 1px 1px 0 #2c1810;
}

.user-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.stat-card {
    background: #2c1810;
    padding: 8px 16px;
    border-radius: 6px;
    border: 1px solid #5c3a2a;
    text-align: center;
}

.stat-label {
    font-size: 11px;
    color: #c9a87b;
    display: block;
}

.stat-value {
    font-size: 20px;
    font-weight: bold;
    color: #ffd700;
}

/* Фильтр панель */
.filter-panel {
    background: #3d2317;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    border: 1px solid #5c3a2a;
}

.filter-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.filter-item {
    flex: 1;
    min-width: 160px;
}

.filter-item label {
    display: block;
    font-size: 11px;
    color: #c9a87b;
    margin-bottom: 5px;
    letter-spacing: 1px;
}

.filter-select {
    width: 100%;
    padding: 10px 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 6px;
    color: #ffd700;
    font-size: 14px;
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
    flex: 1;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
}

.btn-filter {
    background: #8b5e3c;
    color: #ffd700;
}

.btn-filter:hover {
    background: #a0744f;
}

.btn-reset {
    background: #5c3a2a;
    color: #c9a87b;
}

.btn-reset:hover {
    background: #7a4f3a;
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
    border-radius: 8px;
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
    padding: 20px;
    background: #2c1810;
    border-bottom: 1px solid #5c3a2a;
}

.item-image img {
    max-width: 80px;
    max-height: 80px;
}

.item-content {
    padding: 16px;
}

.item-name {
    font-size: 18px;
    font-weight: bold;
    color: #ffd700;
    margin-bottom: 15px;
    text-align: center;
}

/* Списки информации */
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

.stat-row.damage { background: #3d2018; color: #ffaa66; }
.stat-row.defence { background: #1d3d3d; color: #66ccff; }
.stat-row.health { background: #1d3d1d; color: #66ff66; }
.stat-row.mana { background: #2d1d3d; color: #cc66ff; }
.stat-row.crit { background: #3d2d1d; color: #ffd700; }
.stat-row.anticrit { color: #ffaa66; }
.stat-row.fire { background: #3d1d1d; color: #ff8866; }
.stat-row.water { background: #1d3d3d; color: #66ccff; }
.stat-row.air { background: #2d3d3d; color: #66ccff; }
.stat-row.earth { background: #2d3d1d; color: #88ff66; }

/* Блоки */
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

.item-description {
    font-size: 12px;
    color: #c9a87b;
    padding: 8px;
    background: #2c1810;
    border-radius: 4px;
    margin-bottom: 12px;
    font-style: italic;
}

/* Блок покупки */
.purchase-block {
    background: #2c1810;
    border-radius: 6px;
    padding: 12px;
    margin-top: 8px;
}

.purchase-price {
    margin-bottom: 12px;
}

.price-item {
    font-size: 13px;
    padding: 4px 0;
}

.price-item.kr { color: #ffd700; }
.price-item.ekr { color: #cc66ff; }
.price-item.repa { color: #ffaa66; }
.price-item.enough { color: #66ff66; }
.price-item.not-enough { color: #ff6666; }

.purchase-button {
    width: 100%;
}

.btn-buy {
    display: block;
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
}

.btn-kr {
    background: #8b5e3c;
    color: #ffd700;
}

.btn-kr:hover {
    background: #a0744f;
}

.btn-ekr {
    background: #6b3c8b;
    color: #ffccff;
}

.btn-ekr:hover {
    background: #8b4faf;
}

.btn-repa {
    background: #8b4c3c;
    color: #ffd700;
}

.btn-repa:hover {
    background: #af6f5a;
}

.btn-disabled {
    background: #5c3a2a;
    color: #8b7355;
    cursor: not-allowed;
}

/* Пагинация */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination-list {
    display: flex;
    list-style: none;
    gap: 5px;
    flex-wrap: wrap;
}

.pagination-list li a, .pagination-list li span {
    display: block;
    padding: 8px 12px;
    background: #3d2317;
    border: 1px solid #5c3a2a;
    border-radius: 4px;
    color: #c9a87b;
    text-decoration: none;
}

.pagination-list li a:hover {
    background: #8b5e3c;
    color: #ffd700;
    border-color: #ffd700;
}

.pagination-list .active span {
    background: #8b5e3c;
    color: #ffd700;
    border-color: #ffd700;
}

.empty-message {
    text-align: center;
    padding: 50px;
    background: #3d2317;
    border-radius: 8px;
    border: 1px solid #5c3a2a;
    color: #c9a87b;
}

@media (max-width: 768px) {
    .shop-header {
        flex-direction: column;
        text-align: center;
    }
    
    .items-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-item {
        min-width: 100%;
    }
    
    .button-group {
        width: 100%;
    }
}
</style>