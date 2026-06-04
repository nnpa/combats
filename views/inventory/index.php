<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<link rel="stylesheet" href="/css/inventory.css">

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
        
        <!-- ЛЕВАЯ КОЛОНКА: Шлем → Наручи → Оружие → Броня → Пояс -->
        <div class="inventory-left">
            <div class="head" data-slot="helm" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'helm']) ?>">
                <?php if($user->helm !== null): ?>
                    <?php 
                        $helmItem = \app\models\Inventory::findOne($user->helm);
                        if($helmItem && $helmItem->img):
                    ?>
                        <img src="<?= $helmItem->img ?>" alt="Шлем">
                    <?php else: ?>
                        <img src="/img/inv/head.png" alt="Шлем">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/head.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="brasers" data-slot="brasers" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'brasers']) ?>">
                <?php if($user->brasers !== null): ?>
                    <?php 
                        $brasersItem = \app\models\Inventory::findOne($user->brasers);
                        if($brasersItem && $brasersItem->img):
                    ?>
                        <img src="<?= $brasersItem->img ?>" alt="Наручи">
                    <?php else: ?>
                        <img src="/img/inv/brasers.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/brasers.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="weapon" data-slot="weapon" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'weapon']) ?>">
                <?php if($user->weapon !== null): ?>
                    <?php 
                        $weaponItem = \app\models\Inventory::findOne($user->weapon);
                        if($weaponItem && $weaponItem->img):
                    ?>
                        <img src="<?= $weaponItem->img ?>" alt="Оружие">
                    <?php else: ?>
                        <img src="/img/inv/weapon.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/weapon.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="chest" data-slot="chest" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'chest']) ?>">
                <?php if($user->chest !== null): ?>
                    <?php 
                        $chestItem = \app\models\Inventory::findOne($user->chest);
                        if($chestItem && $chestItem->img):
                    ?>
                        <img src="<?= $chestItem->img ?>" alt="Броня">
                    <?php else: ?>
                        <img src="/img/inv/chest.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/chest.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="belt" data-slot="belt" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'belt']) ?>">
                <?php if($user->belt !== null): ?>
                    <?php 
                        $beltItem = \app\models\Inventory::findOne($user->belt);
                        if($beltItem && $beltItem->img):
                    ?>
                        <img src="<?= $beltItem->img ?>" alt="Пояс">
                    <?php else: ?>
                        <img src="/img/inv/belt.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/belt.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- ЦЕНТР (АВАТАР) -->
        <div class="inventory-center">
            <div class="avatar">
                <img src="<?= $user->ava ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">
            </div>
            <!-- Контейнер для эликсиров -->
            <div class="elixir-container">
                <?php foreach($userElexir as $ue): ?>
                    <div class="elixir-item">
                        <img src="<?= $ue->img ?>" class="elixir-icon" alt="Эликсир">
                        <div class="elixir-timer" data-expire="<?= $ue->use_time ?>">
                            <?php 
                                $remaining = $ue->use_time - time();
                                if($remaining > 0){
                                    echo floor($remaining/60).':'.str_pad($remaining%60,2,'0',STR_PAD_LEFT);
                                } else {
                                    echo '0:00';
                                }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- ПРАВАЯ КОЛОНКА: Серьги → Амулет → 3 кольца в строку → Поножи → Щит → Ботинки -->
        <div class="inventory-right">
            <div class="earrings" data-slot="earrings" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'earrings']) ?>">
                <?php if($user->earrings !== null): ?>
                    <?php 
                        $earringsItem = \app\models\Inventory::findOne($user->earrings);
                        if($earringsItem && $earringsItem->img):
                    ?>
                        <img src="<?= $earringsItem->img ?>" alt="Серьги">
                    <?php else: ?>
                        <img src="/img/inv/earrings.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/earrings.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="amulet" data-slot="amulet" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'amulet']) ?>">
                <?php if($user->amulet !== null): ?>
                    <?php 
                        $amuletItem = \app\models\Inventory::findOne($user->amulet);
                        if($amuletItem && $amuletItem->img):
                    ?>
                        <img src="<?= $amuletItem->img ?>" alt="Амулет">
                    <?php else: ?>
                        <img src="/img/inv/amulet.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/amulet.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <!-- 3 КОЛЬЦА В СТРОКУ -->
            <div class="rings" style="display: flex; gap: 0; justify-content: flex-start; margin: 0; padding: 0;">
                <div class="ring" data-slot="ring1" data-undress-url="<?= Url::to(['/inventory/undressring', 'slot' => 'ring1']) ?>" style="margin: 0;">
                    <?php if($user->ring1 !== null): ?>
                        <?php 
                            $ring1Item = \app\models\Inventory::findOne($user->ring1);
                            if($ring1Item && $ring1Item->img):
                        ?>
                            <img src="<?= $ring1Item->img ?>" alt="Кольцо 1">
                        <?php else: ?>
                            <img src="/img/inv/ring.png" alt="Пустой слот">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Пустой слот">
                    <?php endif; ?>
                </div>
                <div class="ring" data-slot="ring2" data-undress-url="<?= Url::to(['/inventory/undressring', 'slot' => 'ring2']) ?>" style="margin: 0;">
                    <?php if($user->ring2 !== null): ?>
                        <?php 
                            $ring2Item = \app\models\Inventory::findOne($user->ring2);
                            if($ring2Item && $ring2Item->img):
                        ?>
                            <img src="<?= $ring2Item->img ?>" alt="Кольцо 2">
                        <?php else: ?>
                            <img src="/img/inv/ring.png" alt="Пустой слот">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Пустой слот">
                    <?php endif; ?>
                </div>
                <div class="ring" data-slot="ring3" data-undress-url="<?= Url::to(['/inventory/undressring', 'slot' => 'ring3']) ?>" style="margin: 0;">
                    <?php if($user->ring3 !== null): ?>
                        <?php 
                            $ring3Item = \app\models\Inventory::findOne($user->ring3);
                            if($ring3Item && $ring3Item->img):
                        ?>
                            <img src="<?= $ring3Item->img ?>" alt="Кольцо 3">
                        <?php else: ?>
                            <img src="/img/inv/ring.png" alt="Пустой слот">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="/img/inv/ring.png" alt="Пустой слот">
                    <?php endif; ?>
                </div>
            </div>
            <div class="gloves" data-slot="gloves" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'gloves']) ?>">
                <?php if($user->gloves !== null): ?>
                    <?php 
                        $glovesItem = \app\models\Inventory::findOne($user->gloves);
                        if($glovesItem && $glovesItem->img):
                    ?>
                        <img src="<?= $glovesItem->img ?>" alt="Перчатки">
                    <?php else: ?>
                        <img src="/img/inv/gloves.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/gloves.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="leg" data-slot="leg" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'leg']) ?>">
                <?php if($user->leg !== null): ?>
                    <?php 
                        $legItem = \app\models\Inventory::findOne($user->leg);
                        if($legItem && $legItem->img):
                    ?>
                        <img src="<?= $legItem->img ?>" alt="Поножи">
                    <?php else: ?>
                        <img src="/img/inv/leg.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/leg.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="shild" data-slot="shild" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'shild']) ?>">
                <?php if($user->shild !== null): ?>
                    <?php 
                        $shildItem = \app\models\Inventory::findOne($user->shild);
                        if($shildItem && $shildItem->img):
                    ?>
                        <img src="<?= $shildItem->img ?>" alt="Щит">
                    <?php else: ?>
                        <img src="/img/inv/shild.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/shild.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
            
            <div class="boots" data-slot="boots" data-undress-url="<?= Url::to(['/inventory/undress', 'type' => 'boots']) ?>">
                <?php if($user->boots !== null): ?>
                    <?php 
                        $bootsItem = \app\models\Inventory::findOne($user->boots);
                        if($bootsItem && $bootsItem->img):
                    ?>
                        <img src="<?= $bootsItem->img ?>" alt="Ботинки">
                    <?php else: ?>
                        <img src="/img/inv/boots.png" alt="Пустой слот">
                    <?php endif; ?>
                <?php else: ?>
                    <img src="/img/inv/boots.png" alt="Пустой слот">
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- СТАТЫ ПОД ИНВЕНТАРЕМ -->
    <div class="stats">
        <div class="stats-title">📊 Характеристики</div>
        <div class="stat-line" data-stat="str">
            <span>💪 Сила:</span>
            <span class="stat-value" id="stat-str"><?= $user->str ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="str">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="dex">
            <span>🏃 Ловкость:</span>
            <span class="stat-value" id="stat-dex"><?= $user->dex ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="dex">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="inte">
            <span>🧠 Интеллект:</span>
            <span class="stat-value" id="stat-inte"><?= $user->inte ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="inte">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="intu">
            <span>✨ Интуиция:</span>
            <span class="stat-value" id="stat-intu"><?= $user->intu ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="intu">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="endu">
            <span>🛡️ Выносливость:</span>
            <span class="stat-value" id="stat-endu"><?= $user->endu ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="endu">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="fire">
            <span>🔥 Огонь:</span>
            <span class="stat-value" id="stat-fire"><?= $user->fire ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="fire">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="water">
            <span>💧 Вода:</span>
            <span class="stat-value" id="stat-water"><?= $user->water ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="water">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="air">
            <span>🌬️ Воздух:</span>
            <span class="stat-value" id="stat-air"><?= $user->air ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="air">+</button>
            <?php endif; ?>
        </div>
        <div class="stat-line" data-stat="earth">
            <span>🌍 Земля:</span>
            <span class="stat-value" id="stat-earth"><?= $user->earth ?></span>
            <?php if($user->points > 0): ?>
                <button class="button-plus" data-stat="earth">+</button>
            <?php endif; ?>
        </div>

        <!-- НОВЫЕ ХАРАКТЕРИСТИКИ (БЕЗ КНОПОК +) -->
        <div class="stat-line">
            <span>❤️ Здоровье:</span>
            <span class="stat-value" id="stat-health"><?= $user->health ?></span>
        </div>
        <div class="stat-line">
            <span>⚔️ Урон:</span>
            <span class="stat-value" id="stat-damage"><?= $user->damage ?></span>
        </div>
        <div class="stat-line">
            <span>🛡️ Защита:</span>
            <span class="stat-value" id="stat-defence"><?= $user->defence ?></span>
        </div>
        <div class="stat-line">
            <span>⭐ Крит:</span>
            <span class="stat-value" id="stat-crit"><?= $user->crit ?></span>
        </div>
        <div class="stat-line">
            <span>🛡️ Антикрит:</span>
            <span class="stat-value" id="stat-anticrit"><?= $user->anticrit ?></span>
        </div>
        <div class="stat-line">
            <span>🔮 Маг. защита:</span>
            <span class="stat-value" id="stat-mdef"><?= $user->mdef ?></span>
        </div>
        <div class="stat-line">
            <span>💨 Уклонение:</span>
            <span class="stat-value" id="stat-evaision"><?= $user->evaision ?></span>
        </div>
        <div class="stat-line">
            <span>🌀 Против уклонения:</span>
            <span class="stat-value" id="stat-aeveision"><?= $user->aeveision ?></span>
        </div>
    </div>
</div>

<!-- Вкладки (Инвентарь, Эликсиры, Заклинания) -->
<div class="tabs-container">
    <div class="tabs-header">
        <button class="tab-button active" data-tab="inventory">📦 Инвентарь</button>
        <button class="tab-button" data-tab="elixirs">🧪 Эликсиры</button>
        <button class="tab-button" data-tab="spells">✨ Заклинания</button>
    </div>
    
    <!-- Вкладка Инвентарь -->
    <div class="tab-content active" id="tab-inventory">
        <div class="row">
            <?php foreach($items as $item): ?>
                <div class="col-sm-6 col-xl-4">
                    <div class="item-card" data-item-id="<?= $item->id ?>">
                        <div class="item-title"><?= Html::encode($item->name) ?></div>
                        <div class="item-image-wrapper">
                            <?php if($item->img): ?>
                                <img src="<?= $item->img ?>" class="item-image" width="80" height="80" alt="<?= Html::encode($item->name) ?>">
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
                <div class="col-12" style="text-align: center; padding: 40px; color: #b89a6a;">
                    📭 У вас нет предметов в инвентаре
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Вкладка Эликсиры -->
    <div class="tab-content" id="tab-elixirs">
        <div class="row">
            <?php foreach($elexir as $elix): ?>
                <div class="col-sm-6 col-xl-4">
                    <div class="item-card">
                        <div class="item-title"><?= Html::encode($elix->name) ?></div>
                        <div class="item-image-wrapper">
                            <?php if($elix->img): ?>
                                <img src="<?= $elix->img ?>" class="item-image" width="80" height="80" alt="<?= Html::encode($elix->name) ?>">
                            <?php endif; ?>
                        </div>
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="<?= Url::to(['/inventory/useelexir', 'id' => $elix->id]) ?>" class="buy-button">🧪 Использовать</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($elexir)): ?>
                <div class="col-12" style="text-align: center; padding: 40px; color: #b89a6a;">
                    🧪 У вас нет эликсиров
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Вкладка Заклинания -->
    <div class="tab-content" id="tab-spells">
        <div class="row">
            <?php foreach($spells as $spell): ?>
                <div class="col-sm-6 col-xl-4">
                    <div class="item-card">
                        <div class="item-title"><?= Html::encode($spell->name) ?></div>
                        <div class="item-image-wrapper">
                            <?php if($spell->img): ?>
                                <img src="<?= $spell->img ?>" class="item-image" width="80" height="80" alt="<?= Html::encode($spell->name) ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($spells)): ?>
                <div class="col-12" style="text-align: center; padding: 40px; color: #b89a6a;">
                    ✨ У вас нет заклинаний
                </div>
            <?php endif; ?>
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

<!-- Стили для модального окна -->
<style>
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

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
body {
    padding-top: 70px;
}

@media (max-width: 768px) {
    body {
        padding-top: 60px;
    }
}

/* Или если меню имеет класс .fixed-top */
.fixed-top + .inventory-wrapper,
header + .inventory-wrapper {
    margin-top: 20px;
    padding-top: 60px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === Снятие предметов со слотов ===
    const slots = document.querySelectorAll('.head, .weapon, .shild, .chest, .leg, .brasers, .belt, .gloves, .boots, .earrings, .amulet, .ring');
    
    slots.forEach(slot => {
        slot.style.cursor = 'pointer';
        
        slot.addEventListener('click', function(e) {
            e.stopPropagation();
            const undressUrl = this.getAttribute('data-undress-url');
            
            if (undressUrl) {
                const img = this.querySelector('img');
                if (img && !img.src.includes('/img/inv/')) {
                    if (confirm('Снять предмет?')) {
                        window.location.href = undressUrl;
                    }
                }
            }
        });
    });
    
    // === Переключение вкладок ===
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            this.classList.add('active');
            const activeContent = document.getElementById('tab-' + tabName);
            if (activeContent) {
                activeContent.classList.add('active');
            }
        });
    });
    
    // === Таймеры для эликсиров (БЕЗ БЕСКОНЕЧНОЙ ПЕРЕЗАГРУЗКИ) ===
    let timerInterval = null;
    let isProcessingExpired = false;
    
    function updateTimers() {
        const timers = document.querySelectorAll('.elixir-timer');
        if (timers.length === 0) {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
            return;
        }
        
        const now = Math.floor(Date.now() / 1000);
        const expiredElixirs = [];
        
        timers.forEach(timer => {
            const expire = parseInt(timer.getAttribute('data-expire'));
            const elixirItem = timer.closest('.elixir-item');
            
            if (expire && expire > now) {
                const remaining = expire - now;
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                timer.textContent = minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
            } else if (expire && expire <= now) {
                if (elixirItem && !elixirItem.hasAttribute('data-expired')) {
                    elixirItem.setAttribute('data-expired', 'true');
                    expiredElixirs.push(elixirItem);
                }
            }
        });
        
        if (expiredElixirs.length > 0 && !isProcessingExpired) {
            isProcessingExpired = true;
            
            // Удаляем визуально
            expiredElixirs.forEach(elixirItem => {
                elixirItem.remove();
            });
            
            // Отправляем запрос на сервер для удаления истекших эликсиров
            fetch('/inventory/removeexpiredelixirs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.removed_count > 0) {
                    // Показываем сообщение
                    const msg = document.createElement('div');
                    msg.textContent = '✨ Эффект эликсира закончился!';
                    msg.style.cssText = 'position:fixed; bottom:20px; right:20px; background:#8b5a22; color:#fff1cc; padding:12px 20px; border-radius:10px; z-index:10000; animation:fadeOutMsg 3s forwards;';
                    document.body.appendChild(msg);
                    setTimeout(() => msg.remove(), 3000);
                    
                    // Обновляем отображаемые статы
                    updateStatsDisplay();
                }
                
                // Если больше нет эликсиров - очищаем интервал
                if (document.querySelectorAll('.elixir-timer').length === 0) {
                    if (timerInterval) {
                        clearInterval(timerInterval);
                        timerInterval = null;
                    }
                }
                
                isProcessingExpired = false;
            })
            .catch(error => {
                console.error('Error:', error);
                isProcessingExpired = false;
            });
        }
    }
    
    // Функция обновления отображаемых статов
    function updateStatsDisplay() {
        fetch('/inventory/getuserstats', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем значения статов на странице
                for (const [stat, value] of Object.entries(data.stats)) {
                    const statElement = document.getElementById('stat-' + stat);
                    if (statElement) {
                        statElement.innerText = value;
                    }
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Запускаем таймеры, если есть эликсиры
    if (document.querySelectorAll('.elixir-timer').length > 0) {
        updateTimers();
        timerInterval = setInterval(updateTimers, 1000);
    }
    
    // === МОДАЛЬНОЕ ОКНО ДЛЯ ДОБАВЛЕНИЯ СТАТОВ ===
    const modal = document.getElementById('statModal');
    const closeBtn = document.querySelector('.modal-close');
    const cancelBtn = document.getElementById('cancelModal');
    const confirmBtn = document.getElementById('confirmAddStat');
    const statAmountInput = document.getElementById('statAmount');
    const maxAmountBtn = document.getElementById('maxAmountBtn');
    const selectedStatNameSpan = document.getElementById('selectedStatName');
    const availablePointsSpan = document.getElementById('availablePoints');
    
    let currentStat = null;
    let currentPoints = parseInt(document.getElementById('points-value').innerText);
    
    // Открытие модального окна при клике на кнопку +
    document.querySelectorAll('.button-plus').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentStat = this.getAttribute('data-stat');
            currentPoints = parseInt(document.getElementById('points-value').innerText);
            
            const statNames = {
                'str': 'Силу',
                'dex': 'Ловкость',
                'inte': 'Интеллект',
                'intu': 'Интуицию',
                'endu': 'Выносливость',
                'fire': 'Огонь',
                'water': 'Воду',
                'air': 'Воздух',
                'earth': 'Землю'
            };
            
            selectedStatNameSpan.innerText = statNames[currentStat] || currentStat;
            availablePointsSpan.innerText = currentPoints;
            statAmountInput.value = 1;
            statAmountInput.max = currentPoints;
            statAmountInput.disabled = (currentPoints === 0);
            maxAmountBtn.disabled = (currentPoints === 0);
            
            modal.style.display = 'block';
        });
    });
    
    // Закрытие модального окна
    function closeModal() {
        modal.style.display = 'none';
        currentStat = null;
    }
    
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    // Клик вне модального окна
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Кнопка MAX
    if (maxAmountBtn) {
        maxAmountBtn.addEventListener('click', function() {
            const maxPoints = parseInt(availablePointsSpan.innerText);
            statAmountInput.value = maxPoints;
        });
    }
    
    // Проверка ввода количества
    if (statAmountInput) {
        statAmountInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            const maxPoints = parseInt(availablePointsSpan.innerText);
            
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > maxPoints) {
                this.value = maxPoints;
                alert('У вас нет столько очков! Доступно: ' + maxPoints);
            }
        });
    }
    
    // Подтверждение добавления статов
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const amount = parseInt(statAmountInput.value);
            
            if (amount < 1) {
                alert('Введите корректное количество (от 1)');
                return;
            }
            
            if (amount > currentPoints) {
                alert('Недостаточно очков! Доступно: ' + currentPoints);
                return;
            }
            
            // Отключим кнопку на время запроса
            confirmBtn.disabled = true;
            confirmBtn.textContent = '⏳ Отправка...';
            
            // Отправка AJAX запроса
            fetch('/inventory/addstats', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'stat=' + currentStat + '&amount=' + amount
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем значения на странице
                    document.getElementById('stat-' + currentStat).innerText = data.newValue;
                    document.getElementById('points-value').innerText = data.remainingPoints;
                    
                    // Обновляем доступные очки в модальном окне
                    availablePointsSpan.innerText = data.remainingPoints;
                    currentPoints = data.remainingPoints;
                    
                    // Если очки закончились - скрываем все кнопки +
                    if (data.remainingPoints <= 0) {
                        document.querySelectorAll('.button-plus').forEach(btn => {
                            btn.style.display = 'none';
                        });
                        statAmountInput.disabled = true;
                        maxAmountBtn.disabled = true;
                    } else {
                        statAmountInput.max = data.remainingPoints;
                    }
                    
                    // Показываем сообщение об успехе
                    const successMsg = document.createElement('div');
                    successMsg.textContent = '✓ Добавлено ' + amount + ' очков к характеристике!';
                    successMsg.style.cssText = 'position:fixed; bottom:20px; right:20px; background:#2d6a2d; color:#ccffcc; padding:12px 20px; border-radius:10px; z-index:10000; animation:fadeOutMsg 3s forwards;';
                    document.body.appendChild(successMsg);
                    setTimeout(() => successMsg.remove(), 3000);
                    
                    closeModal();
                } else {
                    alert(data.error || 'Ошибка при добавлении статов');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при отправке запроса');
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = '✅ Подтвердить';
            });
        });
    }
    
    // === Дополнительно: обновление кнопок при загрузке ===
    function updatePlusButtons() {
        const points = parseInt(document.getElementById('points-value').innerText);
        const plusButtons = document.querySelectorAll('.button-plus');
        
        if (points <= 0) {
            plusButtons.forEach(btn => {
                btn.style.display = 'none';
            });
        } else {
            plusButtons.forEach(btn => {
                btn.style.display = 'inline-block';
            });
        }
    }
    
    updatePlusButtons();
    
    // === Добавляем стили для анимаций ===
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOutMsg {
            0% { opacity: 1; transform: translateY(0); }
            70% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); display: none; }
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
        
        .button-plus:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    `;
    document.head.appendChild(style);
});
</script>