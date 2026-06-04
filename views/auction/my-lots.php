<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Мои лоты';
?>

<div class="auction-container">
    <div class="auction-header">
        <h1>Мои лоты</h1>
        <p class="subtitle">Предметы, выставленные на продажу</p>
    </div>

    <div class="my-lots-section">
        <?php if (empty($items)): ?>
            <div class="empty-message">
                <p>У вас нет выставленных лотов</p>
                <a href="/auction/sell" class="btn-sell">Продать предмет</a>
            </div>
        <?php else: ?>
            <div class="items-grid">
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <div class="item-image">
                            <img src="/<?= $item->img ?>" alt="<?= Html::encode($item->name) ?>">
                        </div>
                        <div class="item-content">
                            <div class="item-name"><?= Html::encode($item->name) ?></div>
                            
                            <div class="item-details">
                                <div class="detail-row">
                                    <span class="detail-label">Уровень:</span>
                                    <span class="detail-value"><?= $item->n_level ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Тип:</span>
                                    <span class="detail-value"><?= Html::encode($item->type) ?></span>
                                </div>
                            </div>
                            
                            <?php if($item->damage > 0): ?>
                                <div class="stat-item">⚔️ Урон: +<?= $item->damage ?></div>
                            <?php endif; ?>
                            <?php if($item->defence > 0): ?>
                                <div class="stat-item">🛡️ Защита: +<?= $item->defence ?></div>
                            <?php endif; ?>
                            <?php if($item->health > 0): ?>
                                <div class="stat-item">❤️ Здоровье: +<?= $item->health ?></div>
                            <?php endif; ?>
                            
                            <div class="lot-price">
                                <span class="price-label">Цена:</span>
                                <span class="price-value"><?= number_format($item->auction->cost ?? 0, 0, ',', ' ') ?> KR</span>
                            </div>
                            
                            <div class="lot-actions">
                                <a href="/auction/remove-lot?id=<?= $item->id ?>" class="btn-remove" onclick="return confirm('Снять предмет с продажи?')">Снять с продажи</a>
                            </div>
                        </div>
                    </div>
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
</div>

<style>
.auction-container {
    width: 100%;
    max-width: 100%;
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
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
}

.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

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

.item-details {
    background: #2c1810;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
}

.detail-label {
    color: #c9a87b;
    font-size: 13px;
}

.detail-value {
    color: #ffd700;
    font-weight: bold;
}

.stat-item {
    padding: 4px 8px;
    margin: 4px 0;
    background: #2c1810;
    border-radius: 4px;
    color: #e6c8a0;
    font-size: 12px;
}

.lot-price {
    background: #2c1810;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin: 12px 0;
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

.btn-remove {
    display: block;
    width: 100%;
    padding: 10px;
    background: #6b2d2d;
    color: #ff6666;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
}

.btn-remove:hover {
    background: #8b3d3d;
}

.btn-sell {
    display: inline-block;
    padding: 10px 20px;
    background: #8b5e3c;
    color: #ffd700;
    border-radius: 8px;
    text-decoration: none;
    margin-top: 15px;
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
}
</style>