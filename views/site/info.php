<?php
use app\models\Clan;
use app\models\ClanUser;
use app\models\Inventory;

// Получаем информацию о клане
$clan = null;
$clanRole = '';
$clanMembersCount = 0;

if ($user->clan_id && $user->clan_id > 0) {
    $clan = Clan::findOne($user->clan_id);
    if ($clan) {
        $clanMembersCount = ClanUser::find()->where(['clan_id' => $clan->id, 'status' => 1])->count();
        if ($clan->admin_id == $user->id) {
            $clanRole = 'Глава клана';
        } else {
            $clanRole = 'Участник клана';
        }
    }
}

// Расчет винрейта
$totalBattles = $user->win + $user->loose;
$winRate = $totalBattles > 0 ? round(($user->win / $totalBattles) * 100) : 0;

// Функция для получения tooltip текста
function getTooltipText($item) {
    if (!$item) return '';
    $tooltip = '';
    if (!empty($item->name)) {
        $tooltip .= $item->name;
    }
    if (!empty($item->description)) {
        $tooltip .= ($tooltip ? "\n" : '') . $item->description;
    }
    return $tooltip;
}
?>

<!-- Инвентарь персонажа -->
<div class="inventory-wrapper">
    <div class="inventory">
        
        <!-- ЛЕВАЯ КОЛОНКА -->
        <div class="inventory-left">
            <div class="head" <?php if($user->helm !== null && $user->helm > 0): $item = Inventory::findOne($user->helm); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->helm !== null && $user->helm > 0): ?>
                    <?php $item = Inventory::findOne($user->helm); ?>
                    <img src="<?= $item->img ?>" alt="Шлем">
                <?php else: ?>
                    <img src="/img/inv/head.png" alt="Шлем">
                <?php endif; ?>
            </div>
            
            <div class="brasers" <?php if($user->brasers !== null && $user->brasers > 0): $item = Inventory::findOne($user->brasers); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->brasers !== null && $user->brasers > 0): ?>
                    <?php $item = Inventory::findOne($user->brasers); ?>
                    <img src="<?= $item->img ?>" alt="Наручи">
                <?php else: ?>
                    <img src="/img/inv/brasers.png" alt="Наручи">
                <?php endif; ?>
            </div>
            
            <div class="weapon" <?php if($user->weapon !== null && $user->weapon > 0): $item = Inventory::findOne($user->weapon); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->weapon !== null && $user->weapon > 0): ?>
                    <?php $item = Inventory::findOne($user->weapon); ?>
                    <img src="<?= $item->img ?>" alt="Оружие">
                <?php else: ?>
                    <img src="/img/inv/weapon.png" alt="Оружие">
                <?php endif; ?>
            </div>
            
            <div class="chest" <?php if($user->chest !== null && $user->chest > 0): $item = Inventory::findOne($user->chest); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->chest !== null && $user->chest > 0): ?>
                    <?php $item = Inventory::findOne($user->chest); ?>
                    <img src="<?= $item->img ?>" alt="Броня">
                <?php else: ?>
                    <img src="/img/inv/chest.png" alt="Броня">
                <?php endif; ?>
            </div>
            
            <div class="belt" <?php if($user->belt !== null && $user->belt > 0): $item = Inventory::findOne($user->belt); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->belt !== null && $user->belt > 0): ?>
                    <?php $item = Inventory::findOne($user->belt); ?>
                    <img src="<?= $item->img ?>" alt="Пояс">
                <?php else: ?>
                    <img src="/img/inv/belt.png" alt="Пояс">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- ЦЕНТР (АВАТАР) -->
        <div class="inventory-center">
            <div class="avatar">
                <img src="<?= $user->ava ?>" alt="Avatar">
            </div>
        </div>
        
        <!-- ПРАВАЯ КОЛОНКА -->
        <div class="inventory-right">
            <div class="earrings" <?php if($user->earrings !== null && $user->earrings > 0): $item = Inventory::findOne($user->earrings); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->earrings !== null && $user->earrings > 0): ?>
                    <?php $item = Inventory::findOne($user->earrings); ?>
                    <img src="<?= $item->img ?>" alt="Серьги">
                <?php else: ?>
                    <img src="/img/inv/earrings.png" alt="Серьги">
                <?php endif; ?>
            </div>
            
            <div class="amulet" <?php if($user->amulet !== null && $user->amulet > 0): $item = Inventory::findOne($user->amulet); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->amulet !== null && $user->amulet > 0): ?>
                    <?php $item = Inventory::findOne($user->amulet); ?>
                    <img src="<?= $item->img ?>" alt="Амулет">
                <?php else: ?>
                    <img src="/img/inv/amulet.png" alt="Амулет">
                <?php endif; ?>
            </div>
            
            <!-- 3 КОЛЬЦА В СТРОКУ -->
            <div class="rings">
                <div class="ring" <?php if($user->ring1 !== null && $user->ring1 > 0): $item = Inventory::findOne($user->ring1); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                    <?php if($user->ring1 !== null && $user->ring1 > 0): ?>
                        <?php $item = Inventory::findOne($user->ring1); ?>
                        <img src="<?= $item->img ?>" alt="Кольцо 1">
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Кольцо">
                    <?php endif; ?>
                </div>
                <div class="ring" <?php if($user->ring2 !== null && $user->ring2 > 0): $item = Inventory::findOne($user->ring2); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                    <?php if($user->ring2 !== null && $user->ring2 > 0): ?>
                        <?php $item = Inventory::findOne($user->ring2); ?>
                        <img src="<?= $item->img ?>" alt="Кольцо 2">
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Кольцо">
                    <?php endif; ?>
                </div>
                <div class="ring" <?php if($user->ring3 !== null && $user->ring3 > 0): $item = Inventory::findOne($user->ring3); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                    <?php if($user->ring3 !== null && $user->ring3 > 0): ?>
                        <?php $item = Inventory::findOne($user->ring3); ?>
                        <img src="<?= $item->img ?>" alt="Кольцо 3">
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Кольцо">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="gloves" <?php if($user->gloves !== null && $user->gloves > 0): $item = Inventory::findOne($user->gloves); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->gloves !== null && $user->gloves > 0): ?>
                    <?php $item = Inventory::findOne($user->gloves); ?>
                    <img src="<?= $item->img ?>" alt="Перчатки">
                <?php else: ?>
                    <img src="/img/inv/gloves.png" alt="Перчатки">
                <?php endif; ?>
            </div>
            
            <div class="shild" <?php if($user->shild !== null && $user->shild > 0): $item = Inventory::findOne($user->shild); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->shild !== null && $user->shild > 0): ?>
                    <?php $item = Inventory::findOne($user->shild); ?>
                    <img src="<?= $item->img ?>" alt="Щит">
                <?php else: ?>
                    <img src="/img/inv/shild.png" alt="Щит">
                <?php endif; ?>
            </div>
            
            <div class="leg" <?php if($user->leg !== null && $user->leg > 0): $item = Inventory::findOne($user->leg); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->leg !== null && $user->leg > 0): ?>
                    <?php $item = Inventory::findOne($user->leg); ?>
                    <img src="<?= $item->img ?>" alt="Поножи">
                <?php else: ?>
                    <img src="/img/inv/leg.png" alt="Поножи">
                <?php endif; ?>
            </div>
            
            <div class="boots" <?php if($user->boots !== null && $user->boots > 0): $item = Inventory::findOne($user->boots); ?> data-tooltip="<?= htmlspecialchars(getTooltipText($item)) ?>" <?php endif; ?>>
                <?php if($user->boots !== null && $user->boots > 0): ?>
                    <?php $item = Inventory::findOne($user->boots); ?>
                    <img src="<?= $item->img ?>" alt="Ботинки">
                <?php else: ?>
                    <img src="/img/inv/boots.png" alt="Ботинки">
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- ХАРАКТЕРИСТИКИ -->
    <div class="stats">
        <div class="stats-title">Характеристики</div>
        <div class="stat-line"><span>Сила</span><span class="stat-value"><?= $user->str ?></span></div>
        <div class="stat-line"><span>Ловкость</span><span class="stat-value"><?= $user->dex ?></span></div>
        <div class="stat-line"><span>Интуиция</span><span class="stat-value"><?= $user->intu ?></span></div>
        <div class="stat-line"><span>Выносливость</span><span class="stat-value"><?= $user->endu ?></span></div>
        <div class="stat-line"><span>Интеллект</span><span class="stat-value"><?= $user->inte ?></span></div>
        <div class="stat-line"><span>Земля</span><span class="stat-value"><?= $user->earth ?></span></div>
        <div class="stat-line"><span>Огонь</span><span class="stat-value"><?= $user->fire ?></span></div>
        <div class="stat-line"><span>Вода</span><span class="stat-value"><?= $user->water ?></span></div>
        <div class="stat-line"><span>Воздух</span><span class="stat-value"><?= $user->air ?></span></div>
    </div>
    
    <!-- ИНФОРМАЦИЯ ОБ ИГРОКЕ -->
    <div class="player-info-card">
        <div class="player-info-header">Информация об игроке</div>
        <div class="player-info-content">
            <div class="info-row">
                <span class="info-label">Имя:</span>
                <span class="info-value"><?= htmlspecialchars($user->username) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Уровень:</span>
                <span class="info-value"><?= $user->level ?></span>
            </div>
        </div>
    </div>
    
    <!-- ИНФОРМАЦИЯ О КЛАНЕ -->
    <div class="clan-info-card">
        <div class="clan-info-header">Клан</div>
        <div class="clan-info-content">
            <?php if ($clan): ?>
                <div class="clan-info-row">
                    <div class="clan-icon">
                        <img src="<?= htmlspecialchars($clan->img) ?>" alt="<?= htmlspecialchars($clan->name) ?>" class="clan-img" onerror="this.src='/img/clan/default.png'">
                    </div>
                    <div class="clan-details">
                        <div class="clan-name"><?= htmlspecialchars($clan->name) ?></div>
                        <div class="clan-role"><?= $clanRole ?></div>
                        <div class="clan-members">Участников: <?= $clanMembersCount ?></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="clan-info-empty">Не состоит в клане</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- PVP СТАТИСТИКА -->
    <div class="stats-pvp-card">
        <div class="stats-pvp-header">PvP Статистика</div>
        <div class="stats-pvp-content">
            <div class="pvp-stat">
                <span class="pvp-label">Победы:</span>
                <span class="pvp-value win"><?= $user->win ?></span>
            </div>
            <div class="pvp-stat">
                <span class="pvp-label">Поражения:</span>
                <span class="pvp-value lose"><?= $user->loose ?></span>
            </div>
            <div class="pvp-stat">
                <span class="pvp-label">Всего боев:</span>
                <span class="pvp-value total"><?= $user->win + $user->loose ?></span>
            </div>
            <div class="pvp-stat">
                <span class="pvp-label">Win Rate:</span>
                <span class="pvp-value winrate"><?= $winRate ?>%</span>
            </div>
            <div class="winrate-bar-container">
                <div class="winrate-bar-fill" style="width: <?= $winRate ?>%"></div>
            </div>
        </div>
    </div>
</div>

<style>
.inventory * {
    padding: 0px;
    margin: 0px;
}

.inventory-left {
    float: left;
    width: 60px;
}

.inventory-center {
    width: 120px;
    min-width: 120px;
    max-width: 120px;
    height: 280px;
    background: linear-gradient(rgba(0,0,0,.35), rgba(0,0,0,.35)), url('/img/inv/player.png');
    background-size: cover;
    background-position: center;
    border: 2px solid #7b5a2f;
    border-radius: 14px;
    box-shadow: inset 0 0 10px rgba(255,215,120,.06), 0 0 10px rgba(0,0,0,.6);
    flex-shrink: 0;
    position: relative;
}

.inventory-center .avatar {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.inventory-center .avatar img {
    max-width: 80%;
    max-height: 80%;
    object-fit: contain;
}

.inventory-right {
    float: left;
    width: 60px;
}

.earrings {
    width: 60px;
    height: 20px;
}

.amulet {
    width: 60px;
    height: 20px;
}

.brasers {
    width: 60px;
    height: 40px;
}

.head {
    width: 60px;
    height: 60px;
}

.chest {
    width: 60px;
    height: 80px;
}

.belt {
    width: 60px;
    height: 40px;
}

.weapon {
    width: 60px;
    height: 60px;
}

.rings {
    display: flex;
    gap: 4px;
}

.ring {
    width: 20px;
    height: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.gloves {
    width: 60px;
    height: 40px;
}

.shild {
    width: 60px;
    height: 60px;
}

.leg {
    width: 60px;
    height: 80px;
}

.boots {
    width: 60px;
    height: 40px;
}

.clearfix {
    content: "";
    display: table;
    clear: both;
}

.stats {
    font-size: 12px;
    color: #222;
    font-family: Verdana, Times, Helvetica, Tahoma;
}

/* ОСНОВА */
.inventory-wrapper {
    width: 280px;
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,.8);
}

.inventory {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.inventory-left,
.inventory-right {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.head,
.weapon,
.shild {
    width: 60px;
    height: 60px;
}

.chest,
.leg {
    width: 60px;
    height: 80px;
}

.brasers,
.belt,
.gloves,
.boots {
    width: 60px;
    height: 40px;
}

.earrings,
.amulet {
    width: 60px;
    height: 20px;
}

.head,
.weapon,
.shild,
.chest,
.leg,
.brasers,
.belt,
.gloves,
.boots,
.earrings,
.amulet,
.ring {
    background: linear-gradient(to bottom, #39281c, #22160f);
    border: 1px solid #8d6737;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: .2s;
    box-shadow: inset 0 0 6px rgba(255,220,120,.05), 0 0 5px rgba(0,0,0,.4);
    position: relative;
}

.head img,
.weapon img,
.shild img,
.chest img,
.leg img,
.brasers img,
.belt img,
.gloves img,
.boots img,
.earrings img,
.amulet img,
.ring img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 0 !important;
    background: white;
    image-rendering: pixelated;
    display: block;
}

.head img[src*="/img/inv/"],
.weapon img[src*="/img/inv/"],
.shild img[src*="/img/inv/"],
.chest img[src*="/img/inv/"],
.leg img[src*="/img/inv/"],
.brasers img[src*="/img/inv/"],
.belt img[src*="/img/inv/"],
.gloves img[src*="/img/inv/"],
.boots img[src*="/img/inv/"],
.earrings img[src*="/img/inv/"],
.amulet img[src*="/img/inv/"],
.ring img[src*="/img/inv/"] {
    background: transparent;
}

.head:hover,
.weapon:hover,
.shild:hover,
.chest:hover,
.leg:hover,
.brasers:hover,
.belt:hover,
.gloves:hover,
.boots:hover,
.earrings:hover,
.amulet:hover,
.ring:hover {
    transform: scale(1.05);
    border-color: #d9b36b;
    box-shadow: 0 0 10px rgba(255,215,120,.25);
}

/* Стили для всплывающих подсказок */
.head[data-tooltip]:hover::after,
.weapon[data-tooltip]:hover::after,
.shild[data-tooltip]:hover::after,
.chest[data-tooltip]:hover::after,
.leg[data-tooltip]:hover::after,
.brasers[data-tooltip]:hover::after,
.belt[data-tooltip]:hover::after,
.gloves[data-tooltip]:hover::after,
.boots[data-tooltip]:hover::after,
.earrings[data-tooltip]:hover::after,
.amulet[data-tooltip]:hover::after,
.ring[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 110%;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(to bottom, #1a1a1a, #0d0d0d);
    color: #ffd27b;
    font-size: 11px;
    font-family: Verdana, Tahoma, sans-serif;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #8d6737;
    white-space: pre-line;
    z-index: 1000;
    min-width: 150px;
    max-width: 250px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.6);
    pointer-events: none;
    word-wrap: break-word;
    line-height: 1.4;
}

/* Стрелка у подсказки */
.head[data-tooltip]:hover::before,
.weapon[data-tooltip]:hover::before,
.shild[data-tooltip]:hover::before,
.chest[data-tooltip]:hover::before,
.leg[data-tooltip]:hover::before,
.brasers[data-tooltip]:hover::before,
.belt[data-tooltip]:hover::before,
.gloves[data-tooltip]:hover::before,
.boots[data-tooltip]:hover::before,
.earrings[data-tooltip]:hover::before,
.amulet[data-tooltip]:hover::before,
.ring[data-tooltip]:hover::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 6px;
    border-style: solid;
    border-color: #8d6737 transparent transparent transparent;
    z-index: 1000;
}

/* Для предметов в правой колонке - подсказка может выезжать влево чтобы не обрезалась */
.inventory-right .ring[data-tooltip]:hover::after,
.inventory-right .earrings[data-tooltip]:hover::after,
.inventory-right .amulet[data-tooltip]:hover::after,
.inventory-right .gloves[data-tooltip]:hover::after,
.inventory-right .shild[data-tooltip]:hover::after,
.inventory-right .leg[data-tooltip]:hover::after,
.inventory-right .boots[data-tooltip]:hover::after {
    left: auto;
    right: 0;
    transform: translateX(0);
}

.inventory-right .ring[data-tooltip]:hover::before,
.inventory-right .earrings[data-tooltip]:hover::before,
.inventory-right .amulet[data-tooltip]:hover::before,
.inventory-right .gloves[data-tooltip]:hover::before,
.inventory-right .shild[data-tooltip]:hover::before,
.inventory-right .leg[data-tooltip]:hover::before,
.inventory-right .boots[data-tooltip]:hover::before {
    left: auto;
    right: 25px;
    transform: translateX(0);
}

.inventory img {
    image-rendering: pixelated;
    display: block;
}

.rings {
    display: flex;
    gap: 4px;
}

.ring {
    width: 20px;
    height: 20px;
    border-radius: 6px;
}

.stats {
    margin-top: 15px;
    background: linear-gradient(to bottom, #24170f, #18100a);
    border: 1px solid #7b5a2f;
    border-radius: 14px;
    padding: 10px;
    color: #d8c08a;
    font-size: 12px;
    box-shadow: inset 0 0 8px rgba(255,220,120,.04);
}

.stats-title {
    text-align: center;
    color: #ffd27b;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 8px;
    text-shadow: 1px 1px 3px #000;
}

.stat-line {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px solid rgba(255,255,255,.05);
}

.stat-line:last-child {
    border-bottom: none;
}

.stat-value {
    color: #ffcf72;
    font-weight: bold;
}

/* Плашка информации о персонаже */
.player-info-card {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    margin-bottom: 15px;
    padding: 12px 15px;
    box-shadow: 0 0 20px rgba(0,0,0,.8);
}

.player-info-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
}

.player-info-item {
    background: rgba(0,0,0,0.4);
    border-radius: 12px;
    padding: 6px 12px;
    min-width: 100px;
    text-align: center;
}

.player-info-label {
    font-size: 10px;
    color: #b89a6a;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.player-info-value {
    font-size: 16px;
    font-weight: bold;
    color: #ffd27b;
}

.player-info-value-small {
    font-size: 12px;
    font-weight: bold;
    color: #ffd27b;
}

.exp-bar-container {
    background: #22160f;
    border-radius: 10px;
    height: 6px;
    width: 100%;
    margin-top: 5px;
    overflow: hidden;
}

.exp-bar-fill {
    background: linear-gradient(to right, #8b5a22, #d2a45b);
    width: 0%;
    height: 100%;
    border-radius: 10px;
    transition: width 0.3s ease;
}

/* Карточки информации */
.player-info-header, .clan-info-header, .stats-pvp-header {
    background: #3a2210;
    padding: 10px 15px;
    color: #ffd27b;
    font-weight: bold;
    border-bottom: 1px solid #7b5a2f;
}

.player-info-content, .clan-info-content, .stats-pvp-content {
    padding: 15px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #3a2a1a;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #b89a6a;
}

.info-value {
    color: #ffd27b;
    font-weight: bold;
}

/* Клан */
.clan-info-row {
    display: flex;
    align-items: center;
    gap: 15px;
}

.clan-icon {
    flex-shrink: 0;
}

.clan-img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid #d2a45b;
}

.clan-details {
    flex: 1;
}

.clan-name {
    color: #ffd27b;
    font-size: 16px;
    font-weight: bold;
}

.clan-role {
    color: #7fc8ff;
    font-size: 11px;
}

.clan-members {
    color: #b89a6a;
    font-size: 11px;
}

.clan-info-empty {
    padding: 15px;
    text-align: center;
    color: #b89a6a;
}

/* PvP */
.pvp-stat {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #3a2a1a;
}

.pvp-stat:last-of-type {
    border-bottom: none;
}

.pvp-label {
    color: #b89a6a;
}

.pvp-value.win {
    color: #9eff9e;
    font-weight: bold;
}

.pvp-value.lose {
    color: #ff8f75;
    font-weight: bold;
}

.pvp-value.total {
    color: #ffd27b;
    font-weight: bold;
}

.pvp-value.winrate {
    color: #7fc8ff;
    font-weight: bold;
}

.winrate-bar-container {
    background: #1a0f0a;
    border-radius: 10px;
    height: 8px;
    width: 100%;
    margin-top: 10px;
    overflow: hidden;
}

.winrate-bar-fill {
    background: linear-gradient(90deg, #2d6a2d, #9eff9e);
    width: 0%;
    height: 100%;
}

/* Адаптация */
@media (max-width: 768px) {
    .inventory {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .player-info-grid {
        flex-direction: column;
    }
    
    .clan-info-row {
        flex-direction: column;
        text-align: center;
    }
    
    .info-row, .pvp-stat, .stat-line {
        flex-direction: column;
        text-align: center;
        gap: 5px;
    }
}
</style>