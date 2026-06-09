<?php
use yii\helpers\Url;
use yii\helpers\Html;

// Проверяем наличие заклинания "Нападение" (spell_id = 2)
$hasAttackSpell = false;
foreach($spells as $spell) {
    if($spell->id == 2) {
        $hasAttackSpell = true;
        break;
    }
}
?>

<link rel="stylesheet" href="/css/inventory.css">

<style>
/* Стили для анимации кнопки */
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255,68,68,0.4); }
    70% { box-shadow: 0 0 0 15px rgba(255,68,68,0); }
    100% { box-shadow: 0 0 0 0 rgba(255,68,68,0); }
}

@keyframes fadeOutMsg {
    0% { opacity: 1; transform: translateY(0); }
    70% { opacity: 1; transform: translateY(0); }
    100% { opacity: 0; transform: translateY(-20px); display: none; }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.button-plus {
    background: linear-gradient(to bottom, #8fff8f, #006400);
    border: 1px solid #d2a45b;
    border-radius: 8px;
    color: #fff1cc;
    padding: 4px 12px;
    font-weight: bold;
    transition: .2s;
    margin-left: 8px;
    cursor: pointer;
    font-size: 12px;
}

.button-plus:hover {
    transform: scale(1.05);
    background: linear-gradient(to bottom, #6fdf6f, #005000);
}

/* Стили для вкладок */
.tabs-container {
    margin-top: 20px;
}

.tabs-header {
    display: flex;
    gap: 10px;
    border-bottom: 2px solid #7b5a2f;
    margin-bottom: 20px;
    padding-bottom: 0;
}

.tab-button {
    background: linear-gradient(to bottom, #3a2817, #21150d);
    border: 1px solid #7b5a2f;
    border-bottom: none;
    border-radius: 10px 10px 0 0;
    color: #d8c08a;
    padding: 12px 25px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 16px;
    font-weight: bold;
}

.tab-button:hover {
    background: linear-gradient(to bottom, #5b3a1d, #3a2210);
    color: #ffd27b;
}

.tab-button.active {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    color: #ffd700;
    border-color: #d2a45b;
    border-bottom: 2px solid #8b5a22;
    margin-bottom: -1px;
}

.tab-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.tab-content.active {
    display: block;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
}

.col-sm-6 {
    width: 50%;
    padding: 0 10px;
    box-sizing: border-box;
}

.col-xl-4 {
    width: 33.333%;
}

.col-12 {
    width: 100%;
    padding: 0 10px;
    box-sizing: border-box;
}

@media (max-width: 768px) {
    .col-sm-6, .col-xl-4 { width: 100%; }
}

.item-card {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 20px;
    transition: all 0.2s;
}

.item-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.item-title {
    color: #ffd27b;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 10px;
}

.item-image-wrapper {
    text-align: center;
    margin-bottom: 10px;
}

.item-image {
    max-width: 80px;
    max-height: 80px;
    border-radius: 10px;
}

.requirements-block, .gives-block {
    margin-top: 10px;
    background: rgba(0,0,0,0.3);
    border-radius: 10px;
    overflow: hidden;
}

.block-header {
    padding: 5px 10px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
}

.require-header {
    background: rgba(180, 60, 30, 0.3);
    color: #ffaa66;
}

.gives-header {
    background: rgba(50, 100, 30, 0.3);
    color: #88ff88;
}

.block-content {
    padding: 8px 10px;
}

.stat-line {
    display: flex;
    justify-content: space-between;
    padding: 3px 0;
    font-size: 11px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.stat-red {
    color: #ff6666;
    font-weight: bold;
}

.stat-green {
    color: #88ff88;
    font-weight: bold;
}

.buy-button {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    border: 1px solid #d2a45b;
    border-radius: 8px;
    color: #fff1cc;
    padding: 6px 12px;
    text-decoration: none;
    font-size: 12px;
    display: inline-block;
    transition: 0.2s;
}

.buy-button:hover {
    background: linear-gradient(to bottom, #a56a2b, #6a3d18);
    transform: scale(1.02);
    color: #ffd700;
}

.player-info-card {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 20px;
}

.player-info-grid {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    justify-content: space-around;
}

.player-info-item {
    text-align: center;
}

.player-info-label {
    color: #b89a6a;
    font-size: 12px;
}

.player-info-value {
    color: #ffd27b;
    font-size: 20px;
    font-weight: bold;
}

.player-info-value-small {
    color: #ffd27b;
    font-size: 12px;
}

.exp-bar-container {
    width: 100px;
    height: 4px;
    background: #4a1a1a;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 5px;
}

.exp-bar-fill {
    background: linear-gradient(to right, #44ff44, #88ff88);
    height: 100%;
    width: 0%;
    transition: width 0.3s;
}

.inventory-wrapper {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 15px;
    margin-bottom: 20px;
}

.inventory {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.inventory-left, .inventory-right {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.inventory-center {
    width: 120px;
    height: 280px;
    background: linear-gradient(rgba(0,0,0,.35), rgba(0,0,0,.35)), url('/img/inv/player.png');
    background-size: cover;
    background-position: center;
    border: 2px solid #7b5a2f;
    border-radius: 14px;
    position: relative;
}

.avatar {
    width: 100%;
    height: 100%;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 14px;
}

.head, .weapon, .shild, .chest, .leg, .brasers, .belt, .gloves, .boots, .earrings, .amulet, .ring {
    background: linear-gradient(to bottom, #39281c, #22160f);
    border: 1px solid #8d6737;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 60px;
}

.head { height: 60px; }
.weapon { height: 60px; }
.shild { height: 60px; }
.chest { height: 80px; }
.leg { height: 80px; }
.brasers { height: 40px; }
.belt { height: 40px; }
.gloves { height: 40px; }
.boots { height: 40px; }
.earrings { height: 20px; }
.amulet { height: 20px; }
.ring { width: 20px; height: 20px; }

.rings {
    display: flex;
    gap: 4px;
}

.head img, .weapon img, .shild img, .chest img, .leg img, 
.brasers img, .belt img, .gloves img, .boots img, 
.earrings img, .amulet img, .ring img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 0;
    background: white;
    image-rendering: pixelated;
}

.stats {
    margin-top: 15px;
    background: linear-gradient(to bottom, #24170f, #18100a);
    border: 1px solid #7b5a2f;
    border-radius: 14px;
    padding: 10px;
    color: #d8c08a;
    font-size: 12px;
}

.stats-title {
    text-align: center;
    color: #ffd27b;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 8px;
}

.elixir-container {
    position: absolute;
    bottom: 5px;
    left: 5px;
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.elixir-item {
    position: relative;
    width: 32px;
    height: 32px;
}

.elixir-icon {
    width: 100%;
    height: 100%;
    border-radius: 5px;
    border: 1px solid #ffd700;
}

.elixir-timer {
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 8px;
    color: #ffd700;
    background: rgba(0,0,0,0.6);
    padding: 1px 3px;
    border-radius: 3px;
    white-space: nowrap;
}

/* Модальное окно для статов */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    animation: fadeIn 0.3s;
}

.modal-content {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 20px;
    width: 400px;
    max-width: 90%;
    margin: 100px auto;
    box-shadow: 0 0 30px rgba(0,0,0,0.9);
    animation: slideIn 0.3s;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #7b5a2f;
    position: relative;
}

.modal-header h2 {
    color: #ffd27b;
    margin: 0;
    font-size: 22px;
    text-align: center;
}

.modal-close {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 28px;
    font-weight: bold;
    color: #b89a6a;
    cursor: pointer;
    transition: 0.2s;
}

.modal-close:hover {
    color: #ffd27b;
    transform: scale(1.2);
}

.modal-body {
    padding: 20px;
}

.modal-body p {
    color: #d8c08a;
    margin-bottom: 15px;
    font-size: 14px;
}

.modal-body strong {
    color: #ffd27b;
}

.modal-input-group {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.modal-input-group label {
    color: #d8c08a;
    font-weight: bold;
}

.modal-input {
    flex: 1;
    padding: 8px 12px;
    background: #22160f;
    border: 1px solid #7b5a2f;
    border-radius: 10px;
    color: #ffd27b;
    font-size: 16px;
    text-align: center;
}

.modal-max-btn {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    border: 1px solid #d2a45b;
    border-radius: 10px;
    color: #fff1cc;
    padding: 8px 15px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.2s;
}

.modal-max-btn:hover {
    background: linear-gradient(to bottom, #a56a2b, #6a3d18);
    transform: scale(1.05);
}

.modal-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
}

.modal-btn {
    padding: 10px 25px;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
    font-size: 14px;
}

.confirm-btn {
    background: linear-gradient(to bottom, #2d6a2d, #1a4a1a);
    border: 1px solid #5a9e5a;
    color: #ccffcc;
}

.confirm-btn:hover {
    background: linear-gradient(to bottom, #3d8a3d, #2a5a2a);
    transform: scale(1.05);
}

.cancel-btn {
    background: linear-gradient(to bottom, #8b3a2a, #5a2518);
    border: 1px solid #c06040;
    color: #ffccaa;
}

.cancel-btn:hover {
    background: linear-gradient(to bottom, #ab4a3a, #6a2d1a);
    transform: scale(1.05);
}

body {
    padding-top: 70px;
}

@media (max-width: 768px) {
    body {
        padding-top: 60px;
    }
}
</style>

<!-- Кнопка Нападения (показывается только если есть заклинание) -->
<?php if($hasAttackSpell): ?>
<div style="text-align: center; margin-bottom: 20px;">
    <button id="attackButtonMain" style="background: linear-gradient(135deg, #8b1a1a, #4a0a0a); border: 2px solid #ff4444; border-radius: 50px; color: #ffd700; padding: 12px 30px; font-size: 18px; font-weight: bold; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 0 15px rgba(255,0,0,0.3); animation: pulse 2s infinite;">
        ⚔️ НАПАСТЬ НА ИГРОКА ⚔️
    </button>
</div>
<?php endif; ?>

<!-- Плашка с информацией о персонаже -->
<div class="player-info-card">
    <div class="player-info-grid">
        <div class="player-info-item">
            <div class="player-info-label">Уровень</div>
            <div class="player-info-value"><?= $user->level ?></div>
        </div>
        <div class="player-info-item">
            <div class="player-info-label">UP</div>
            <div class="player-info-value"><?= $user->up ?></div>
        </div>
        <div class="player-info-item">
            <div class="player-info-label">Опыт</div>
            <div class="player-info-value-small"><?= $user->exp ?> / <?= $nextLevelUp ? $nextLevelUp->exp_required : 'MAX' ?></div>
            <div class="exp-bar-container">
                <?php
                $expPercent = 0;
                if ($nextLevelUp && $currentLevelUp) {
                    $expNeeded = $nextLevelUp->exp_required - $currentLevelUp->exp_required;
                    $expCurrent = $user->exp - $currentLevelUp->exp_required;
                    if ($expNeeded > 0) {
                        $expPercent = ($expCurrent / $expNeeded) * 100;
                    }
                }
                ?>
                <div class="exp-bar-fill" style="width: <?= $expPercent ?>%;"></div>
            </div>
        </div>
        <div class="player-info-item">
            <div class="player-info-label">Очки статов</div>
            <div class="player-info-value" id="points-value"><?= $user->points ?></div>
        </div>
    </div>
</div>

<!-- Инвентарь персонажа -->
<div class="inventory-wrapper">
    <div class="inventory">
        
        <!-- ЛЕВАЯ КОЛОНКА -->
        <div class="inventory-left">
            <div class="head" data-slot="helm" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'helm']) ?>">
                <?php if($user->helm !== null): ?>
                    <?php $helmItem = \app\models\Inventory::findOne($user->helm); ?>
                    <img src="<?= $helmItem && $helmItem->img ? $helmItem->img : '/img/inv/head.png' ?>" alt="Шлем">
                <?php else: ?>
                    <img src="/img/inv/head.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="brasers" data-slot="brasers" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'brasers']) ?>">
                <?php if($user->brasers !== null): ?>
                    <?php $brasersItem = \app\models\Inventory::findOne($user->brasers); ?>
                    <img src="<?= $brasersItem && $brasersItem->img ? $brasersItem->img : '/img/inv/brasers.png' ?>" alt="Наручи">
                <?php else: ?>
                    <img src="/img/inv/brasers.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="weapon" data-slot="weapon" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'weapon']) ?>">
                <?php if($user->weapon !== null): ?>
                    <?php $weaponItem = \app\models\Inventory::findOne($user->weapon); ?>
                    <img src="<?= $weaponItem && $weaponItem->img ? $weaponItem->img : '/img/inv/weapon.png' ?>" alt="Оружие">
                <?php else: ?>
                    <img src="/img/inv/weapon.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="chest" data-slot="chest" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'chest']) ?>">
                <?php if($user->chest !== null): ?>
                    <?php $chestItem = \app\models\Inventory::findOne($user->chest); ?>
                    <img src="<?= $chestItem && $chestItem->img ? $chestItem->img : '/img/inv/chest.png' ?>" alt="Броня">
                <?php else: ?>
                    <img src="/img/inv/chest.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="belt" data-slot="belt" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'belt']) ?>">
                <?php if($user->belt !== null): ?>
                    <?php $beltItem = \app\models\Inventory::findOne($user->belt); ?>
                    <img src="<?= $beltItem && $beltItem->img ? $beltItem->img : '/img/inv/belt.png' ?>" alt="Пояс">
                <?php else: ?>
                    <img src="/img/inv/belt.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- ЦЕНТР (АВАТАР) -->
        <div class="inventory-center">
            <div class="avatar">
                <img src="<?= $user->ava ?>" alt="Avatar">
            </div>
            <div class="elixir-container">
                <?php foreach($userElexir as $ue): ?>
                    <div class="elixir-item">
                        <img src="<?= $ue->img ?>" class="elixir-icon" alt="Эликсир">
                        <div class="elixir-timer" data-expire="<?= $ue->use_time ?>">
                            <?php 
                                $remaining = $ue->use_time - time();
                                echo $remaining > 0 ? floor($remaining/60).':'.str_pad($remaining%60,2,'0',STR_PAD_LEFT) : '0:00';
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- ПРАВАЯ КОЛОНКА -->
        <div class="inventory-right">
            <div class="earrings" data-slot="earrings" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'earrings']) ?>">
                <?php if($user->earrings !== null): ?>
                    <?php $earringsItem = \app\models\Inventory::findOne($user->earrings); ?>
                    <img src="<?= $earringsItem && $earringsItem->img ? $earringsItem->img : '/img/inv/earrings.png' ?>" alt="Серьги">
                <?php else: ?>
                    <img src="/img/inv/earrings.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="amulet" data-slot="amulet" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'amulet']) ?>">
                <?php if($user->amulet !== null): ?>
                    <?php $amuletItem = \app\models\Inventory::findOne($user->amulet); ?>
                    <img src="<?= $amuletItem && $amuletItem->img ? $amuletItem->img : '/img/inv/amulet.png' ?>" alt="Амулет">
                <?php else: ?>
                    <img src="/img/inv/amulet.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="rings">
                <div class="ring" data-slot="ring1" data-undress-url="<?= Url::to(['/inventory/undressring', 'slot' => 'ring1']) ?>">
                    <?php if($user->ring1 !== null): ?>
                        <?php $ring1Item = \app\models\Inventory::findOne($user->ring1); ?>
                        <img src="<?= $ring1Item && $ring1Item->img ? $ring1Item->img : '/img/inv/ring.png' ?>" alt="Кольцо">
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Пустой слот">
                    <?php endif; ?>
                </div>
                <div class="ring" data-slot="ring2" data-undress-url="<?= Url::to(['/inventory/undressring', 'slot' => 'ring2']) ?>">
                    <?php if($user->ring2 !== null): ?>
                        <?php $ring2Item = \app\models\Inventory::findOne($user->ring2); ?>
                        <img src="<?= $ring2Item && $ring2Item->img ? $ring2Item->img : '/img/inv/ring.png' ?>" alt="Кольцо">
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Пустой слот">
                    <?php endif; ?>
                </div>
                <div class="ring" data-slot="ring3" data-undress-url="<?= Url::to(['/inventory/undressring', 'slot' => 'ring3']) ?>">
                    <?php if($user->ring3 !== null): ?>
                        <?php $ring3Item = \app\models\Inventory::findOne($user->ring3); ?>
                        <img src="<?= $ring3Item && $ring3Item->img ? $ring3Item->img : '/img/inv/ring.png' ?>" alt="Кольцо">
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Пустой слот">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="gloves" data-slot="gloves" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'gloves']) ?>">
                <?php if($user->gloves !== null): ?>
                    <?php $glovesItem = \app\models\Inventory::findOne($user->gloves); ?>
                    <img src="<?= $glovesItem && $glovesItem->img ? $glovesItem->img : '/img/inv/gloves.png' ?>" alt="Перчатки">
                <?php else: ?>
                    <img src="/img/inv/gloves.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="leg" data-slot="leg" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'leg']) ?>">
                <?php if($user->leg !== null): ?>
                    <?php $legItem = \app\models\Inventory::findOne($user->leg); ?>
                    <img src="<?= $legItem && $legItem->img ? $legItem->img : '/img/inv/leg.png' ?>" alt="Поножи">
                <?php else: ?>
                    <img src="/img/inv/leg.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="shild" data-slot="shild" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'shild']) ?>">
                <?php if($user->shild !== null): ?>
                    <?php $shildItem = \app\models\Inventory::findOne($user->shild); ?>
                    <img src="<?= $shildItem && $shildItem->img ? $shildItem->img : '/img/inv/shild.png' ?>" alt="Щит">
                <?php else: ?>
                    <img src="/img/inv/shild.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="boots" data-slot="boots" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'boots']) ?>">
                <?php if($user->boots !== null): ?>
                    <?php $bootsItem = \app\models\Inventory::findOne($user->boots); ?>
                    <img src="<?= $bootsItem && $bootsItem->img ? $bootsItem->img : '/img/inv/boots.png' ?>" alt="Ботинки">
                <?php else: ?>
                    <img src="/img/inv/boots.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- СТАТЫ ПОД ИНВЕНТАРЕМ -->
    <div class="stats">
        <div class="stats-title">📊 Характеристики</div>
        <div class="stat-line" data-stat="str"><span>💪 Сила:</span><span class="stat-value" id="stat-str"><?= $user->str ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="str">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="dex"><span>🏃 Ловкость:</span><span class="stat-value" id="stat-dex"><?= $user->dex ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="dex">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="inte"><span>🧠 Интеллект:</span><span class="stat-value" id="stat-inte"><?= $user->inte ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="inte">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="intu"><span>✨ Интуиция:</span><span class="stat-value" id="stat-intu"><?= $user->intu ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="intu">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="endu"><span>🛡️ Выносливость:</span><span class="stat-value" id="stat-endu"><?= $user->endu ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="endu">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="fire"><span>🔥 Огонь:</span><span class="stat-value" id="stat-fire"><?= $user->fire ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="fire">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="water"><span>💧 Вода:</span><span class="stat-value" id="stat-water"><?= $user->water ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="water">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="air"><span>🌬️ Воздух:</span><span class="stat-value" id="stat-air"><?= $user->air ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="air">+</button><?php endif; ?></div>
        <div class="stat-line" data-stat="earth"><span>🌍 Земля:</span><span class="stat-value" id="stat-earth"><?= $user->earth ?></span><?php if($user->points > 0): ?><button class="button-plus" data-stat="earth">+</button><?php endif; ?></div>
        <div class="stat-line"><span>❤️ Здоровье:</span><span class="stat-value" id="stat-health"><?= $user->health ?></span></div>
        <div class="stat-line"><span>⚔️ Урон:</span><span class="stat-value" id="stat-damage"><?= $user->damage ?></span></div>
        <div class="stat-line"><span>🛡️ Защита:</span><span class="stat-value" id="stat-defence"><?= $user->defence ?></span></div>
        <div class="stat-line"><span>⭐ Крит:</span><span class="stat-value" id="stat-crit"><?= $user->crit ?></span></div>
        <div class="stat-line"><span>🛡️ Антикрит:</span><span class="stat-value" id="stat-anticrit"><?= $user->anticrit ?></span></div>
        <div class="stat-line"><span>🔮 Маг. защита:</span><span class="stat-value" id="stat-mdef"><?= $user->mdef ?></span></div>
        <div class="stat-line"><span>💨 Уклонение:</span><span class="stat-value" id="stat-evaision"><?= $user->evaision ?></span></div>
        <div class="stat-line"><span>🌀 Против уклонения:</span><span class="stat-value" id="stat-aeveision"><?= $user->aeveision ?></span></div>
    </div>
</div>

<!-- Вкладки -->
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-button active" data-tab="inventory">📦 Инвентарь</button>
        <button class="tab-button" data-tab="elixirs">🧪 Эликсиры</button>
        <button class="tab-button" data-tab="spells">✨ Заклинания</button>
    </div>
    
    <div class="tab-content active" id="tab-inventory">
        <div class="row">
            <?php foreach($items as $item): ?>
                <div class="col-sm-6 col-xl-4">
                    <div class="item-card">
                        <div class="item-title"><?= Html::encode($item->name) ?></div>
                        <div class="item-image-wrapper">
                            <?php if($item->img): ?>
                                <img src="<?= $item->img ?>" class="item-image" alt="<?= Html::encode($item->name) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="requirements-block">
                            <div class="block-header require-header">⚔️ Требования</div>
                            <div class="block-content">
                                <?php if($item->n_level > 0): ?><div class="stat-line"><span>Уровень:</span><span class="stat-red"><?= $item->n_level ?></span></div><?php endif; ?>
                                <?php if($item->n_str > 0): ?><div class="stat-line"><span>Сила:</span><span class="stat-red"><?= $item->n_str ?></span></div><?php endif; ?>
                                <?php if($item->n_dex > 0): ?><div class="stat-line"><span>Ловкость:</span><span class="stat-red"><?= $item->n_dex ?></span></div><?php endif; ?>
                                <?php if($item->n_end > 0): ?><div class="stat-line"><span>Выносливость:</span><span class="stat-red"><?= $item->n_end ?></span></div><?php endif; ?>
                                <?php if($item->n_inte > 0): ?><div class="stat-line"><span>Интеллект:</span><span class="stat-red"><?= $item->n_inte ?></span></div><?php endif; ?>
                                <?php if($item->n_intu > 0): ?><div class="stat-line"><span>Интуиция:</span><span class="stat-red"><?= $item->n_intu ?></span></div><?php endif; ?>
                            </div>
                        </div>
                        <div class="gives-block">
                            <div class="block-header gives-header">✨ Даёт</div>
                            <div class="block-content">
                                <?php if($item->str > 0): ?><div class="stat-line"><span>Сила:</span><span class="stat-green">+<?= $item->str ?></span></div><?php endif; ?>
                                <?php if($item->dex > 0): ?><div class="stat-line"><span>Ловкость:</span><span class="stat-green">+<?= $item->dex ?></span></div><?php endif; ?>
                                <?php if($item->end > 0): ?><div class="stat-line"><span>Выносливость:</span><span class="stat-green">+<?= $item->end ?></span></div><?php endif; ?>
                                <?php if($item->inte > 0): ?><div class="stat-line"><span>Интеллект:</span><span class="stat-green">+<?= $item->inte ?></span></div><?php endif; ?>
                                <?php if($item->intu > 0): ?><div class="stat-line"><span>Интуиция:</span><span class="stat-green">+<?= $item->intu ?></span></div><?php endif; ?>
                                <?php if($item->damage > 0): ?><div class="stat-line"><span>Урон:</span><span class="stat-green">+<?= $item->damage ?></span></div><?php endif; ?>
                                <?php if($item->defence > 0): ?><div class="stat-line"><span>Защита:</span><span class="stat-green">+<?= $item->defence ?></span></div><?php endif; ?>
                                <?php if($item->health > 0): ?><div class="stat-line"><span>Здоровье:</span><span class="stat-green">+<?= $item->health ?></span></div><?php endif; ?>
                                <?php if($item->crit > 0): ?><div class="stat-line"><span>Крит:</span><span class="stat-green">+<?= $item->crit ?></span></div><?php endif; ?>
                            </div>
                        </div>
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="<?= Url::to(['/inventory/dress', 'id' => $item->id]) ?>" class="buy-button">🔨 Экипировать</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($items)): ?>
                <div class="col-12" style="text-align: center; padding: 40px; color: #b89a6a;">📭 У вас нет предметов в инвентаре</div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="tab-content" id="tab-elixirs">
        <div class="row">
            <?php foreach($elexir as $elix): ?>
                <div class="col-sm-6 col-xl-4">
                    <div class="item-card">
                        <div class="item-title"><?= Html::encode($elix->name) ?></div>
                        <div class="item-image-wrapper">
                            <?php if($elix->img): ?>
                                <img src="<?= $elix->img ?>" class="item-image" alt="<?= Html::encode($elix->name) ?>">
                            <?php endif; ?>
                        </div>
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="<?= Url::to(['/inventory/useelexir', 'id' => $elix->id]) ?>" class="buy-button">🧪 Использовать</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($elexir)): ?>
                <div class="col-12" style="text-align: center; padding: 40px; color: #b89a6a;">🧪 У вас нет эликсиров</div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="tab-content" id="tab-spells">
        <div class="row">
            <?php foreach($spells as $spell): ?>
                <div class="col-sm-6 col-xl-4">
                    <div class="item-card">
                        <div class="item-title"><?= Html::encode($spell->name) ?></div>
                        <div class="item-image-wrapper">
                            <?php if($spell->img): ?>
                                <img src="<?= $spell->img ?>" class="item-image" alt="<?= Html::encode($spell->name) ?>">
                            <?php endif; ?>
                        </div>
                        <?php if($spell->id == 2): ?>
                            <div style="text-align: center; margin-top: 15px;">
                                <button class="attack-from-spell-btn" data-spell-id="<?= $spell->id ?>" style="background: linear-gradient(to bottom, #8b1a1a, #4a0a0a); border: 1px solid #ff4444; border-radius: 10px; color: #ffd700; padding: 8px 15px; cursor: pointer; font-weight: bold; width: 100%;">⚔️ ИСПОЛЬЗОВАТЬ (Нападение)</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($spells)): ?>
                <div class="col-12" style="text-align: center; padding: 40px; color: #b89a6a;">✨ У вас нет заклинаний</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- МОДАЛЬНОЕ ОКНО ДЛЯ АТАКИ -->
<div id="attackModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; justify-content: center; align-items: center;">
    <div style="background: linear-gradient(135deg, #2b1d12, #1f150c); border: 2px solid #ff4444; border-radius: 20px; width: 400px; max-width: 90%; padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #ff4444; background: #3d2317; border-radius: 18px 18px 0 0;">
            <h2 style="color: #ffd700; margin: 0; font-size: 22px;">⚔️ НАПАДЕНИЕ</h2>
            <button id="closeModalBtn" style="background: none; border: none; color: #ffd700; font-size: 28px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 20px;">
            <p style="color: #c9a87b; margin-bottom: 15px;">Введите имя игрока, на которого хотите напасть:</p>
            
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="text" id="attackUsername" placeholder="Имя игрока" style="flex: 1; padding: 12px; background: #2c1810; border: 1px solid #ff4444; border-radius: 8px; color: #ffd700; font-size: 14px;">
                <button id="checkPlayerBtn" style="background: #5c3a2a; color: #c9a87b; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer;">🔍 Проверить</button>
            </div>
            
            <div id="playerInfoBlock" style="display: none; background: rgba(0,0,0,0.5); border-radius: 10px; padding: 15px; margin-bottom: 15px; border: 1px solid #ff4444;">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <img id="modalPlayerAvatar" src="" style="width: 60px; height: 60px; border-radius: 10px; border: 2px solid #ffd700;">
                    <div style="flex: 1;">
                        <div id="modalPlayerName" style="font-size: 18px; font-weight: bold; color: #ffd700; margin-bottom: 5px;"></div>
                        <div style="font-size: 12px; color: #c9a87b;">📊 Уровень: <span id="modalPlayerLevel" style="color: #ffd700;"></span></div>
                        <div style="font-size: 12px; color: #c9a87b;">❤️ Здоровье: <span id="modalPlayerHealth" style="color: #ffd700;"></span></div>
                        <div style="font-size: 12px; color: #c9a87b;">⚔️ Урон: <span id="modalPlayerDamage" style="color: #ffd700;"></span></div>
                        <div style="font-size: 12px; color: #c9a87b;">🛡️ Защита: <span id="modalPlayerDefence" style="color: #ffd700;"></span></div>
                    </div>
                </div>
            </div>
            
            <div id="modalErrorMsg" style="display: none; background: rgba(255,0,0,0.2); border: 1px solid #ff4444; border-radius: 8px; padding: 10px; color: #ff8888; margin-bottom: 15px; text-align: center;"></div>
            
            <div id="attackButtonsBlock" style="display: none; gap: 10px;">
                <button id="confirmAttackBtn" style="flex: 1; background: #8b1a1a; color: #ffd700; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold;">⚔️ НАПАСТЬ</button>
                <button id="cancelAttackBtn" style="flex: 1; background: #5c3a2a; color: #c9a87b; border: none; padding: 12px; border-radius: 8px; cursor: pointer;">Отмена</button>
            </div>
        </div>
    </div>
</div>

<!-- МОДАЛЬНОЕ ОКНО ДЛЯ ДОБАВЛЕНИЯ СТАТОВ -->
<div id="statModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close">&times;</span>
            <h2>➕ Добавить очки характеристик</h2>
        </div>
        <div class="modal-body">
            <p>Выберите количество очков для добавления в характеристику <strong id="selectedStatName"></strong></p>
            <p>Доступно очков: <strong id="availablePoints"></strong></p>
            
            <div class="modal-input-group">
                <label for="statAmount">Количество:</label>
                <input type="number" id="statAmount" min="1" value="1" class="modal-input">
                <button id="maxAmountBtn" class="modal-max-btn">MAX</button>
            </div>
            
            <div class="modal-buttons">
                <button id="confirmAddStat" class="modal-btn confirm-btn">✅ Подтвердить</button>
                <button id="cancelModal" class="modal-btn cancel-btn">❌ Отмена</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === ПЕРЕКЛЮЧЕНИЕ ВКЛАДОК ===
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            this.classList.add('active');
            const activeContent = document.getElementById('tab-' + tabName);
            if (activeContent) activeContent.classList.add('active');
        });
    });
    
    // === СНЯТИЕ ПРЕДМЕТОВ ===
    document.querySelectorAll('.head, .weapon, .shild, .chest, .leg, .brasers, .belt, .gloves, .boots, .earrings, .amulet, .ring').forEach(slot => {
        slot.style.cursor = 'pointer';
        slot.addEventListener('click', function(e) {
            e.stopPropagation();
            const undressUrl = this.getAttribute('data-undress-url');
            if (undressUrl) {
                const img = this.querySelector('img');
                if (img && !img.src.includes('/img/inv/')) {
                    if (confirm('Снять предмет?')) window.location.href = undressUrl;
                }
            }
        });
    });
    
    // === ТАЙМЕРЫ ЭЛИКСИРОВ ===
    let timerInterval = null;
    function updateTimers() {
        const timers = document.querySelectorAll('.elixir-timer');
        if (timers.length === 0) {
            if (timerInterval) clearInterval(timerInterval);
            return;
        }
        const now = Math.floor(Date.now() / 1000);
        timers.forEach(timer => {
            const expire = parseInt(timer.getAttribute('data-expire'));
            if (expire && expire > now) {
                const remaining = expire - now;
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                timer.textContent = minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
            }
        });
    }
    if (document.querySelectorAll('.elixir-timer').length > 0) {
        updateTimers();
        timerInterval = setInterval(updateTimers, 1000);
    }
    
    // === МОДАЛЬНОЕ ОКНО ДЛЯ АТАКИ ===
    const attackModal = document.getElementById('attackModal');
    const attackUsername = document.getElementById('attackUsername');
    const checkPlayerBtn = document.getElementById('checkPlayerBtn');
    const confirmAttackBtn = document.getElementById('confirmAttackBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelAttackBtn = document.getElementById('cancelAttackBtn');
    const playerInfoBlock = document.getElementById('playerInfoBlock');
    const modalErrorMsg = document.getElementById('modalErrorMsg');
    const attackButtonsBlock = document.getElementById('attackButtonsBlock');
    
    function showAttackModal() {
        attackModal.style.display = 'flex';
        attackUsername.value = '';
        playerInfoBlock.style.display = 'none';
        modalErrorMsg.style.display = 'none';
        attackButtonsBlock.style.display = 'none';
    }
    
    function closeAttackModal() {
        attackModal.style.display = 'none';
    }
    
    const attackButtonMain = document.getElementById('attackButtonMain');
    if (attackButtonMain) attackButtonMain.addEventListener('click', showAttackModal);
    
    document.querySelectorAll('.attack-from-spell-btn').forEach(btn => {
        btn.addEventListener('click', showAttackModal);
    });
    
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeAttackModal);
    if (cancelAttackBtn) cancelAttackBtn.addEventListener('click', closeAttackModal);
    attackModal.addEventListener('click', function(e) { if (e.target === attackModal) closeAttackModal(); });
    
    if (checkPlayerBtn) {
        checkPlayerBtn.addEventListener('click', function() {
            const username = attackUsername.value.trim();
            if (!username) {
                modalErrorMsg.textContent = 'Введите имя игрока';
                modalErrorMsg.style.display = 'block';
                return;
            }
            
            fetch('/spell/check-player', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'username=' + encodeURIComponent(username)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalPlayerName').textContent = data.player.username;
                    document.getElementById('modalPlayerLevel').textContent = data.player.level;
                    document.getElementById('modalPlayerHealth').textContent = data.player.health;
                    document.getElementById('modalPlayerDamage').textContent = data.player.damage;
                    document.getElementById('modalPlayerDefence').textContent = data.player.defence;
                    document.getElementById('modalPlayerAvatar').src = data.player.avatar || '/img/default-avatar.png';
                    playerInfoBlock.style.display = 'block';
                    modalErrorMsg.style.display = 'none';
                    attackButtonsBlock.style.display = 'flex';
                } else {
                    modalErrorMsg.textContent = data.error;
                    modalErrorMsg.style.display = 'block';
                    playerInfoBlock.style.display = 'none';
                    attackButtonsBlock.style.display = 'none';
                }
            })
            .catch(error => {
                modalErrorMsg.textContent = 'Ошибка при проверке игрока';
                modalErrorMsg.style.display = 'block';
            });
        });
    }
    
    if (confirmAttackBtn) {
        confirmAttackBtn.addEventListener('click', function() {
            const username = attackUsername.value.trim();
            if (!username) return;
            
            confirmAttackBtn.disabled = true;
            confirmAttackBtn.textContent = '⏳ Отправка...';
            
            fetch('/spell/attack', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'username=' + encodeURIComponent(username)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('⚔️ ' + data.message);
                    window.location.href = data.redirect;
                } else {
                    modalErrorMsg.textContent = data.error;
                    modalErrorMsg.style.display = 'block';
                    confirmAttackBtn.disabled = false;
                    confirmAttackBtn.textContent = '⚔️ НАПАСТЬ';
                }
            })
            .catch(error => {
                modalErrorMsg.textContent = 'Ошибка при атаке';
                modalErrorMsg.style.display = 'block';
                confirmAttackBtn.disabled = false;
                confirmAttackBtn.textContent = '⚔️ НАПАСТЬ';
            });
        });
    }
    
    // === МОДАЛЬНОЕ ОКНО ДЛЯ СТАТОВ ===
    const statModal = document.getElementById('statModal');
    const statModalClose = document.querySelector('.modal-close');
    const cancelModal = document.getElementById('cancelModal');
    const confirmAddStat = document.getElementById('confirmAddStat');
    const statAmount = document.getElementById('statAmount');
    const maxAmountBtn = document.getElementById('maxAmountBtn');
    const selectedStatNameSpan = document.getElementById('selectedStatName');
    const availablePointsSpan = document.getElementById('availablePoints');
    let currentStat = null;
    let currentPoints = parseInt(document.getElementById('points-value').innerText);
    
    document.querySelectorAll('.button-plus').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentStat = this.getAttribute('data-stat');
            currentPoints = parseInt(document.getElementById('points-value').innerText);
            const statNames = {
                'str': 'Силу', 'dex': 'Ловкость', 'inte': 'Интеллект',
                'intu': 'Интуицию', 'endu': 'Выносливость', 'fire': 'Огонь',
                'water': 'Воду', 'air': 'Воздух', 'earth': 'Землю'
            };
            selectedStatNameSpan.innerText = statNames[currentStat] || currentStat;
            availablePointsSpan.innerText = currentPoints;
            statAmount.value = 1;
            statAmount.max = currentPoints;
            statModal.style.display = 'block';
        });
    });
    
    function closeStatModal() { statModal.style.display = 'none'; currentStat = null; }
    if (statModalClose) statModalClose.addEventListener('click', closeStatModal);
    if (cancelModal) cancelModal.addEventListener('click', closeStatModal);
    window.addEventListener('click', function(e) { if (e.target === statModal) closeStatModal(); });
    
    if (maxAmountBtn) {
        maxAmountBtn.addEventListener('click', function() {
            statAmount.value = parseInt(availablePointsSpan.innerText);
        });
    }
    
    if (confirmAddStat) {
        confirmAddStat.addEventListener('click', function() {
            const amount = parseInt(statAmount.value);
            if (amount < 1 || amount > currentPoints) {
                alert('Некорректное количество');
                return;
            }
            confirmAddStat.disabled = true;
            confirmAddStat.textContent = '⏳ Отправка...';
            fetch('/inventory/addstats', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'stat=' + currentStat + '&amount=' + amount
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('stat-' + currentStat).innerText = data.newValue;
                    document.getElementById('points-value').innerText = data.remainingPoints;
                    currentPoints = data.remainingPoints;
                    if (data.remainingPoints <= 0) {
                        document.querySelectorAll('.button-plus').forEach(btn => btn.style.display = 'none');
                    }
                    closeStatModal();
                } else {
                    alert(data.error || 'Ошибка');
                }
            })
            .catch(error => { alert('Ошибка'); })
            .finally(() => {
                confirmAddStat.disabled = false;
                confirmAddStat.textContent = '✅ Подтвердить';
            });
        });
    }
});
</script>