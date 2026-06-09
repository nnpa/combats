<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Артефакты и заклинания';
?>

<div class="shop-container">
    <div class="shop-header">
        <h1>Артефакты и заклинания</h1>
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

    <!-- Заклинание -->
    <?php if ($spell): ?>
    <div class="spell-section">
        <h2 class="section-title">📜 Заклинания</h2>
        <div class="spell-card">
            <div class="spell-image">
                <img src="<?= $spell->img ?>" alt="<?= Html::encode($spell->name) ?>">
            </div>
            <div class="spell-info">
                <div class="spell-name"><?= Html::encode($spell->name) ?></div>
                <div class="spell-price">💎 Цена: 1 EKR</div>
            </div>
            <div class="spell-button">
                <a class="btn-buy-spell" 
                   href="/shop/buy-spell?id=<?= $spell->id ?>" 
                   onclick="return confirm('Купить заклинание «<?= Html::encode($spell->name) ?>» за 1 EKR?');">
                    🛒 Купить за 1 EKR
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Артефакты -->
    <?php if (!empty($items)): ?>
        <h2 class="section-title">✨ Артефакты</h2>
        <div class="items-grid">
            <?php foreach ($items as $item): ?>
                <div class="item-card">
                    <div class="item-image">
                        <img src="<?= $item->img ?>" alt="<?= Html::encode($item->name) ?>">
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
                                <?php if($item->cost_ekr > 0): ?>
                                    <div class="price-item ekr <?= $user->ekr >= $item->cost_ekr ? 'enough' : 'not-enough' ?>">
                                        EKR: <?= number_format($item->cost_ekr, 0, ',', ' ') ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($item->cost > 0): ?>
                                    <div class="price-item kr">KR: <?= number_format($item->cost, 0, ',', ' ') ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="purchase-button">
                                <?php if($item->cost_ekr > 0 && $user->ekr >= $item->cost_ekr): ?>
                                    <a class="btn-buy btn-ekr" 
                                       href="/shop/buyekr?id=<?= $item->id ?>" 
                                       onclick="return confirm('Купить артефакт <?= Html::encode($item->name) ?> за <?= number_format($item->cost_ekr, 0, ',', ' ') ?> EKR?');">
                                        Купить за EKR
                                    </a>
                                <?php elseif($item->cost > 0 && $user->kr >= $item->cost): ?>
                                    <a class="btn-buy btn-kr" 
                                       href="/shop/buyekr?id=<?= $item->id ?>" 
                                       onclick="return confirm('Купить артефакт <?= Html::encode($item->name) ?> за <?= number_format($item->cost, 0, ',', ' ') ?> KR?');">
                                        Купить за KR
                                    </a>
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
    <?php else: ?>
        <div class="empty-message">
            <p>Артефактов пока нет</p>
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

/* Стили для заклинания */
.spell-section {
    margin-bottom: 30px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
    padding: 20px;
}

.section-title {
    color: #ffd700;
    font-size: 20px;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #5c3a2a;
}

.spell-card {
    display: flex;
    align-items: center;
    gap: 20px;
    background: #2c1810;
    border-radius: 10px;
    padding: 15px;
    flex-wrap: wrap;
}

.spell-image {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
}

.spell-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.spell-info {
    flex: 1;
}

.spell-name {
    font-size: 18px;
    font-weight: bold;
    color: #ffd700;
    margin-bottom: 5px;
}

.spell-price {
    font-size: 14px;
    font-weight: bold;
    color: #cc66ff;
    margin-top: 8px;
}

.spell-button {
    flex-shrink: 0;
}

.btn-buy-spell {
    display: block;
    padding: 10px 20px;
    background: #6b3c8b;
    color: #ffccff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.2s;
}

.btn-buy-spell:hover {
    background: #8b4faf;
    transform: scale(1.02);
}

@media (max-width: 768px) {
    .shop-header {
        flex-direction: column;
        text-align: center;
    }
    
    .items-grid {
        grid-template-columns: 1fr;
    }
    
    .spell-card {
        flex-direction: column;
        text-align: center;
    }
    
    .spell-info {
        text-align: center;
    }
}
</style>