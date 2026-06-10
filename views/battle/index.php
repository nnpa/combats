<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>

<style>
.battle-container {
    display: flex;
    gap: 20px;
    padding: 20px;
    max-width: 1400px;
    margin: 0 auto;
}

.battle-left {
    width: 280px;
    flex-shrink: 0;
}

.battle-center {
    flex: 1;
    min-width: 500px;
}

.battle-right {
    width: 280px;
    flex-shrink: 0;
}

/* Команды */
.teams-container {
    display: flex;
    gap: 20px;
    justify-content: space-between;
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 15px;
    margin-bottom: 20px;
}

.team {
    flex: 1;
    text-align: center;
}

.team-title {
    font-size: 20px;
    font-weight: bold;
    color: #ffd27b;
    margin-bottom: 15px;
}

.team-members {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.team-member {
    background: rgba(0,0,0,0.4);
    border-radius: 10px;
    padding: 8px;
}

.member-name {
    color: #d8c08a;
    font-size: 14px;
}

.member-name a {
    color: #ffd27b;
    text-decoration: none;
}

.member-name a:hover {
    text-decoration: underline;
}

.member-hp {
    color: #ffaa66;
    font-size: 12px;
    margin-top: 4px;
}

/* Боевые действия */
.battle-actions {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 20px;
}

.action-row {
    display: flex;
    gap: 30px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.action-col {
    flex: 1;
    min-width: 150px;
}

.action-col h4 {
    color: #ffd27b;
    margin-bottom: 10px;
    font-size: 16px;
}

.radio-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.radio-group label {
    color: #d8c08a;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.radio-group input[type="radio"] {
    cursor: pointer;
    width: 16px;
    height: 16px;
}

.skill-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
    margin-bottom: 0;
}

.skill-btn {
    background: linear-gradient(to bottom, #3a2817, #21150d);
    border: 1px solid #7b5a2f;
    border-radius: 10px;
    color: #d8c08a;
    padding: 8px 15px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
}

.skill-btn:hover {
    background: linear-gradient(to bottom, #5b3a1d, #3a2210);
    color: #ffd27b;
    transform: scale(1.02);
}

.skill-btn.fire { border-color: #ff4444; color: #ff8888; }
.skill-btn.earth { border-color: #ffaa44; color: #ffcc88; }
.skill-btn.water { border-color: #44ff44; color: #88ff88; }
.skill-btn.air { border-color: #44aaff; color: #88ccff; }

.action-btn {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    border: 2px solid #d2a45b;
    border-radius: 12px;
    color: #fff1cc;
    padding: 10px 20px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
    font-size: 16px;
    margin-top: 10px;
}

.action-btn:hover {
    background: linear-gradient(to bottom, #a56a2b, #6a3d18);
    transform: scale(1.02);
}

.action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.reload-btn {
    background: linear-gradient(to bottom, #2d5a8a, #1a3a5a);
    border: 2px solid #5a9ece;
    margin-top: 10px;
}

.reload-btn:hover {
    background: linear-gradient(to bottom, #3d7aaa, #2a4a6a);
}

/* Логи */
.logs-container {
    background: linear-gradient(to bottom, #1a0f08, #0f0805);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 15px;
    height: 300px;
    overflow-y: auto;
}

.logs-title {
    color: #ffd27b;
    margin-bottom: 10px;
    font-size: 16px;
    font-weight: bold;
}

.log-entry {
    padding: 5px 10px;
    border-bottom: 1px solid #3a2817;
    font-size: 12px;
    font-family: monospace;
}

.log-entry.system {
    color: #88aaff;
}

.log-entry.damage {
    color: #ffaa88;
}

.log-entry.skill {
    color: #88ff88;
}

.log-entry.critical {
    color: #ff6666;
    font-weight: bold;
}

/* Инвентарь противника */
.enemy-inventory {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 15px;
}

.enemy-title {
    color: #ffd27b;
    font-size: 18px;
    margin-bottom: 15px;
    text-align: center;
}

/* Сообщение об ожидании хода противника */
.waiting-message {
    text-align: center;
    padding: 50px;
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #ffd27b;
    border-radius: 18px;
    margin-bottom: 20px;
}

.waiting-message h2 {
    color: #ffd27b;
    margin-bottom: 20px;
}

.waiting-message p {
    color: #d8c08a;
    margin-bottom: 15px;
}

.waiting-spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid #7b5a2f;
    border-top-color: #ffd27b;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Блокировка действий */
.actions-disabled {
    opacity: 0.5;
    pointer-events: none;
}

/* Инвентарь */
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

/* Прогресс бар здоровья */
.health-bar-container {
    margin-bottom: 10px;
    padding: 0 5px;
}

.health-bar-bg {
    background: #4a1a1a;
    border-radius: 6px;
    height: 8px;
    overflow: hidden;
    position: relative;
}

.health-bar-fill {
    background: linear-gradient(to right, #44ff44, #88ff88);
    border-radius: 6px;
    height: 100%;
    width: 100%;
    transition: width 0.3s ease;
}

.health-bar-fill.danger {
    background: linear-gradient(to right, #ff4444, #ff8888);
}

.health-text {
    font-size: 10px;
    color: #d8c08a;
    text-align: center;
    margin-top: 3px;
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

.stat-line {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px solid rgba(255,255,255,.05);
}

.stat-value {
    color: #ffcf72;
    font-weight: bold;
}
</style>

<div class="battle-container">
    <!-- ЛЕВАЯ КОЛОНКА - Инвентарь игрока -->
    <div class="battle-left">
        <div class="inventory-wrapper">
            <!-- Прогресс бар здоровья игрока -->
            <?php
                $currentHp = $battleUser->hp ?? $user->health;
                $maxHp = $user->health;
                $hpPercent = ($currentHp / $maxHp) * 100;
                $hpClass = $hpPercent < 30 ? 'danger' : '';
            ?>
            <div class="health-bar-container">
                <div class="health-bar-bg">
                    <div class="health-bar-fill <?= $hpClass ?>" style="width: <?= $hpPercent ?>%;"></div>
                </div>
                <div class="health-text">❤️ <?= $currentHp ?> / <?= $maxHp ?></div>
            </div>
            
            <div class="inventory">
                <!-- Левая колонка -->
                <div class="inventory-left">
                    <div class="head">
                        <?php if($user->helm && $helmItem = \app\models\Inventory::findOne($user->helm)): ?>
                            <img src="<?= $helmItem->img ?>" alt="Шлем">
                        <?php else: ?>
                            <img src="/img/inv/head.png" alt="Шлем">
                        <?php endif; ?>
                    </div>
                    <div class="brasers">
                        <?php if($user->brasers && $brasersItem = \app\models\Inventory::findOne($user->brasers)): ?>
                            <img src="<?= $brasersItem->img ?>" alt="Наручи">
                        <?php else: ?>
                            <img src="/img/inv/brasers.png" alt="Наручи">
                        <?php endif; ?>
                    </div>
                    <div class="weapon">
                        <?php if($user->weapon && $weaponItem = \app\models\Inventory::findOne($user->weapon)): ?>
                            <img src="<?= $weaponItem->img ?>" alt="Оружие">
                        <?php else: ?>
                            <img src="/img/inv/weapon.png" alt="Оружие">
                        <?php endif; ?>
                    </div>
                    <div class="chest">
                        <?php if($user->chest && $chestItem = \app\models\Inventory::findOne($user->chest)): ?>
                            <img src="<?= $chestItem->img ?>" alt="Броня">
                        <?php else: ?>
                            <img src="/img/inv/chest.png" alt="Броня">
                        <?php endif; ?>
                    </div>
                    <div class="belt">
                        <?php if($user->belt && $beltItem = \app\models\Inventory::findOne($user->belt)): ?>
                            <img src="<?= $beltItem->img ?>" alt="Пояс">
                        <?php else: ?>
                            <img src="/img/inv/belt.png" alt="Пояс">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Центр - аватар -->
                <div class="inventory-center">
                    <div class="avatar">
                        <img src="<?= $user->ava ?>" alt="Avatar">
                    </div>
                </div>
                
                <!-- Правая колонка -->
                <div class="inventory-right">
                    <div class="earrings">
                        <?php if($user->earrings && $earringsItem = \app\models\Inventory::findOne($user->earrings)): ?>
                            <img src="<?= $earringsItem->img ?>" alt="Серьги">
                        <?php else: ?>
                            <img src="/img/inv/earrings.png" alt="Серьги">
                        <?php endif; ?>
                    </div>
                    <div class="amulet">
                        <?php if($user->amulet && $amuletItem = \app\models\Inventory::findOne($user->amulet)): ?>
                            <img src="<?= $amuletItem->img ?>" alt="Амулет">
                        <?php else: ?>
                            <img src="/img/inv/amulet.png" alt="Амулет">
                        <?php endif; ?>
                    </div>
                    <div class="rings">
                        <div class="ring">
                            <?php if($user->ring1 && $ring1Item = \app\models\Inventory::findOne($user->ring1)): ?>
                                <img src="<?= $ring1Item->img ?>" alt="Кольцо">
                            <?php else: ?>
                                <img src="/img/inv/ring.png" alt="Кольцо">
                            <?php endif; ?>
                        </div>
                        <div class="ring">
                            <?php if($user->ring2 && $ring2Item = \app\models\Inventory::findOne($user->ring2)): ?>
                                <img src="<?= $ring2Item->img ?>" alt="Кольцо">
                            <?php else: ?>
                                <img src="/img/inv/ring.png" alt="Кольцо">
                            <?php endif; ?>
                        </div>
                        <div class="ring">
                            <?php if($user->ring3 && $ring3Item = \app\models\Inventory::findOne($user->ring3)): ?>
                                <img src="<?= $ring3Item->img ?>" alt="Кольцо">
                            <?php else: ?>
                                <img src="/img/inv/ring.png" alt="Кольцо">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="leg">
                        <?php if($user->leg && $legItem = \app\models\Inventory::findOne($user->leg)): ?>
                            <img src="<?= $legItem->img ?>" alt="Поножи">
                        <?php else: ?>
                            <img src="/img/inv/leg.png" alt="Поножи">
                        <?php endif; ?>
                    </div>
                    <div class="shild">
                        <?php if($user->shild && $shildItem = \app\models\Inventory::findOne($user->shild)): ?>
                            <img src="<?= $shildItem->img ?>" alt="Щит">
                        <?php else: ?>
                            <img src="/img/inv/shild.png" alt="Щит">
                        <?php endif; ?>
                    </div>
                    <div class="boots">
                        <?php if($user->boots && $bootsItem = \app\models\Inventory::findOne($user->boots)): ?>
                            <img src="<?= $bootsItem->img ?>" alt="Ботинки">
                        <?php else: ?>
                            <img src="/img/inv/boots.png" alt="Ботинки">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Характеристики (без здоровья) -->
            <div class="stats">
                <div class="stats-title">📊 Характеристики</div>
                <div class="stat-line"><span>⚔️ Урон:</span><span class="stat-value"><?= $user->damage ?></span></div>
                <div class="stat-line"><span>🛡️ Защита:</span><span class="stat-value"><?= $user->defence ?></span></div>
                <div class="stat-line"><span>💪 Сила:</span><span class="stat-value"><?= $user->str ?></span></div>
                <div class="stat-line"><span>🏃 Ловкость:</span><span class="stat-value"><?= $user->dex ?></span></div>
                <div class="stat-line"><span>✨ Интуиция:</span><span class="stat-value"><?= $user->intu ?></span></div>
                <div class="stat-line"><span>🧠 Интеллект:</span><span class="stat-value"><?= $user->inte ?></span></div>
                <div class="stat-line"><span>🔥 Огонь:</span><span class="stat-value"><?= $user->fire ?></span></div>
                <div class="stat-line"><span>💧 Вода:</span><span class="stat-value"><?= $user->water ?></span></div>
                <div class="stat-line"><span>🌬️ Воздух:</span><span class="stat-value"><?= $user->air ?></span></div>
                <div class="stat-line"><span>🌍 Земля:</span><span class="stat-value"><?= $user->earth ?></span></div>
            </div>
        </div>
    </div>
    
    <!-- ЦЕНТР - Управление боем -->
    <div class="battle-center">
        <?php if($battleEnemy === null): ?>
            <!-- Ожидание хода противника -->
            <div class="waiting-message">
                <div class="waiting-spinner"></div>
                <h2>⏳ Ожидание хода противника...</h2>
                <p>Противник еще не сделал свой ход</p>
                <button class="action-btn reload-btn" onclick="reloadPage()">🔄 Обновить</button>
            </div>
            
            <!-- Логи боя -->
            <div class="logs-container">
                <div class="logs-title">📜 Лог боя</div>
                <?php if(!empty($logs)): ?>
                    <?php foreach($logs as $log): ?>
                        <div class="log-entry <?= strpos($log->log, 'КРИТ') !== false ? 'critical' : (strpos($log->log, 'использовал') !== false ? 'skill' : 'damage') ?>">
                            <?= $log->log ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="log-entry system">Бой начался... Ожидайте ход противника</div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Есть противник - показываем полный интерфейс боя -->
            
            <!-- Команды -->
            <div class="teams-container">
                <div class="team">
                    <div class="team-title">👥 Команда 1</div>
                    <div class="team-members">
                        <?php
                            $team1Members = \app\models\UserBattle::find()
                                ->where(['battle_id' => $user->battle_id, 'komand' => 1])
                                ->all();
                            foreach($team1Members as $member):
                                $memberUser = null;
                                if($member->user_id){
                                    $memberUser = \app\models\User::findOne($member->user_id);
                                } elseif($member->bot_id){
                                    $memberUser = \app\models\User::findOne($member->bot_id);
                                }
                                if($memberUser):
                        ?>
                            <div class="team-member">
                                <div class="member-name">
                                    <a href="<?= Url::to(['/site/info', 'username' => $memberUser->username]) ?>" target="_blank">
                                        <?= Html::encode($memberUser->username) ?>
                                        <?php if($member->bot_id): ?> (бот)<?php endif; ?>
                                    </a>
                                </div>
                                <div class="member-hp">❤️ HP: <?= $member->hp ?></div>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <div class="team">
                    <div class="team-title">👥 Команда 2</div>
                    <div class="team-members">
                        <?php
                            $team2Members = \app\models\UserBattle::find()
                                ->where(['battle_id' => $user->battle_id, 'komand' => 2])
                                ->all();
                            foreach($team2Members as $member):
                                $memberUser = null;
                                if($member->user_id){
                                    $memberUser = \app\models\User::findOne($member->user_id);
                                } elseif($member->bot_id){
                                    $memberUser = \app\models\User::findOne($member->bot_id);
                                }
                                if($memberUser):
                        ?>
                            <div class="team-member">
                                <div class="member-name">
                                    <a href="<?= Url::to(['/site/info', 'username' => $memberUser->username]) ?>" target="_blank">
                                        <?= Html::encode($memberUser->username) ?>
                                        <?php if($member->bot_id): ?> (бот)<?php endif; ?>
                                    </a>
                                </div>
                                <div class="member-hp">❤️ HP: <?= $member->hp ?></div>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Боевые действия -->
            <div class="battle-actions">
                <div class="action-row">
                    <div class="action-col">
                        <h4>⚔️ Атака</h4>
                        <div class="radio-group" id="attack-group">
                            <label><input type="radio" name="attack" value="1" checked> 🎯 Голова</label>
                            <label><input type="radio" name="attack" value="2"> 🎯 Корпус</label>
                            <label><input type="radio" name="attack" value="3"> 🎯 Пах</label>
                            <label><input type="radio" name="attack" value="4"> 🎯 Ноги</label>
                        </div>
                    </div>
                    <div class="action-col">
                        <h4>🛡️ Защита</h4>
                        <div class="radio-group" id="defence-group">
                            <label><input type="radio" name="defence" value="1" checked> 🛡️ Голова + Корпус</label>
                            <label><input type="radio" name="defence" value="2"> 🛡️ Корпус + Пах</label>
                            <label><input type="radio" name="defence" value="3"> 🛡️ Пах + Ноги</label>
                            <label><input type="radio" name="defence" value="4"> 🛡️ Ноги + Голова</label>
                        </div>
                    </div>
                </div>
                
                <button class="action-btn" id="attack-btn">⚔️ СДЕЛАТЬ ХОД</button>
                
                <div class="skill-buttons">
                    <button class="skill-btn fire" data-skill="fire">🔥 Метеорит</button>
                    <button class="skill-btn earth" data-skill="earth">🪨 Камнепад</button>
                    <button class="skill-btn water" data-skill="water">💚 Лечение</button>
                    <button class="skill-btn air" data-skill="air">🛡️ Щит</button>
                </div>
            </div>
            
            <!-- Логи -->
            <div class="logs-container">
                <div class="logs-title">📜 Лог боя</div>
                <?php if(!empty($logs)): ?>
                    <?php foreach($logs as $log): ?>
                        <div class="log-entry <?= strpos($log->log, 'КРИТ') !== false ? 'critical' : (strpos($log->log, 'использовал') !== false ? 'skill' : 'damage') ?>">
                            <?= $log->log ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="log-entry system">Бой начался... Сделайте свой ход!</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- ПРАВАЯ КОЛОНКА - Инвентарь противника (только если есть противник) -->
    <?php if($battleEnemy !== null && $enemy !== null): 
        $enemyCurrentHp = $battleEnemy->hp ?? $enemy->health;
        $enemyMaxHp = $enemy->health;
        $enemyHpPercent = ($enemyCurrentHp / $enemyMaxHp) * 100;
        $enemyHpClass = $enemyHpPercent < 30 ? 'danger' : '';
    ?>
    <div class="battle-right">
        <div class="enemy-inventory">
            <div class="enemy-title">👤 <?= Html::encode($enemy->username) ?></div>
            
            <!-- Прогресс бар здоровья противника -->
            <div class="health-bar-container">
                <div class="health-bar-bg">
                    <div class="health-bar-fill <?= $enemyHpClass ?>" style="width: <?= $enemyHpPercent ?>%;"></div>
                </div>
                <div class="health-text">❤️ <?= $enemyCurrentHp ?> / <?= $enemyMaxHp ?></div>
            </div>
            
            <div class="inventory-wrapper" style="margin: 0; padding: 10px;">
                <div class="inventory">
                    <div class="inventory-left">
                        <div class="head">
                            <?php if($enemy->helm && $helmItem = \app\models\Inventory::findOne($enemy->helm)): ?>
                                <img src="<?= $helmItem->img ?>" alt="Шлем">
                            <?php else: ?>
                                <img src="/img/inv/head.png" alt="Шлем">
                            <?php endif; ?>
                        </div>
                        <div class="brasers">
                            <?php if($enemy->brasers && $brasersItem = \app\models\Inventory::findOne($enemy->brasers)): ?>
                                <img src="<?= $brasersItem->img ?>" alt="Наручи">
                            <?php else: ?>
                                <img src="/img/inv/brasers.png" alt="Наручи">
                            <?php endif; ?>
                        </div>
                        <div class="weapon">
                            <?php if($enemy->weapon && $weaponItem = \app\models\Inventory::findOne($enemy->weapon)): ?>
                                <img src="<?= $weaponItem->img ?>" alt="Оружие">
                            <?php else: ?>
                                <img src="/img/inv/weapon.png" alt="Оружие">
                            <?php endif; ?>
                        </div>
                        <div class="chest">
                            <?php if($enemy->chest && $chestItem = \app\models\Inventory::findOne($enemy->chest)): ?>
                                <img src="<?= $chestItem->img ?>" alt="Броня">
                            <?php else: ?>
                                <img src="/img/inv/chest.png" alt="Броня">
                            <?php endif; ?>
                        </div>
                        <div class="belt">
                            <?php if($enemy->belt && $beltItem = \app\models\Inventory::findOne($enemy->belt)): ?>
                                <img src="<?= $beltItem->img ?>" alt="Пояс">
                            <?php else: ?>
                                <img src="/img/inv/belt.png" alt="Пояс">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="inventory-center">
                        <div class="avatar">
                            <img src="<?= $enemy->ava ?>" alt="Avatar">
                        </div>
                    </div>
                    
                    <div class="inventory-right">
                        <div class="earrings">
                            <?php if($enemy->earrings && $earringsItem = \app\models\Inventory::findOne($enemy->earrings)): ?>
                                <img src="<?= $earringsItem->img ?>" alt="Серьги">
                            <?php else: ?>
                                <img src="/img/inv/earrings.png" alt="Серьги">
                            <?php endif; ?>
                        </div>
                        <div class="amulet">
                            <?php if($enemy->amulet && $amuletItem = \app\models\Inventory::findOne($enemy->amulet)): ?>
                                <img src="<?= $amuletItem->img ?>" alt="Амулет">
                            <?php else: ?>
                                <img src="/img/inv/amulet.png" alt="Амулет">
                            <?php endif; ?>
                        </div>
                        <div class="rings">
                            <div class="ring">
                                <?php if($enemy->ring1 && $ring1Item = \app\models\Inventory::findOne($enemy->ring1)): ?>
                                    <img src="<?= $ring1Item->img ?>" alt="Кольцо">
                                <?php else: ?>
                                    <img src="/img/inv/ring.png" alt="Кольцо">
                                <?php endif; ?>
                            </div>
                            <div class="ring">
                                <?php if($enemy->ring2 && $ring2Item = \app\models\Inventory::findOne($enemy->ring2)): ?>
                                    <img src="<?= $ring2Item->img ?>" alt="Кольцо">
                                <?php else: ?>
                                    <img src="/img/inv/ring.png" alt="Кольцо">
                                <?php endif; ?>
                            </div>
                            <div class="ring">
                                <?php if($enemy->ring3 && $ring3Item = \app\models\Inventory::findOne($enemy->ring3)): ?>
                                    <img src="<?= $ring3Item->img ?>" alt="Кольцо">
                                <?php else: ?>
                                    <img src="/img/inv/ring.png" alt="Кольцо">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="leg">
                            <?php if($enemy->leg && $legItem = \app\models\Inventory::findOne($enemy->leg)): ?>
                                <img src="<?= $legItem->img ?>" alt="Поножи">
                            <?php else: ?>
                                <img src="/img/inv/leg.png" alt="Поножи">
                            <?php endif; ?>
                        </div>
                        <div class="shild">
                            <?php if($enemy->shild && $shildItem = \app\models\Inventory::findOne($enemy->shild)): ?>
                                <img src="<?= $shildItem->img ?>" alt="Щит">
                            <?php else: ?>
                                <img src="/img/inv/shild.png" alt="Щит">
                            <?php endif; ?>
                        </div>
                        <div class="boots">
                            <?php if($enemy->boots && $bootsItem = \app\models\Inventory::findOne($enemy->boots)): ?>
                                <img src="<?= $bootsItem->img ?>" alt="Ботинки">
                            <?php else: ?>
                                <img src="/img/inv/boots.png" alt="Ботинки">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="stats">
                    <div class="stats-title">📊 Характеристики</div>
                    <div class="stat-line"><span>⚔️ Урон:</span><span class="stat-value"><?= $enemy->damage ?></span></div>
                    <div class="stat-line"><span>🛡️ Защита:</span><span class="stat-value"><?= $enemy->defence ?></span></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Получаем ID противника
const enemyId = <?= $enemy ? $enemy->id : 0 ?>;

// Функция перезагрузки страницы
function reloadPage() {
    location.reload();
}

// Функция отправки запроса без ожидания ответа
function sendAction(url, button) {
    // Блокируем кнопку, чтобы не нажали дважды
    if (button) {
        button.disabled = true;
        button.textContent = '⏳ Отправка...';
    }
    
    // Отправляем запрос, но не ждем ответа
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).catch(error => {
        console.error('Fetch error:', error);
    });
    
    // Сразу перезагружаем страницу через 0.5 секунды
    setTimeout(function() {
        reloadPage();
    }, 500);
}

// === Обработчики для обычной атаки (только если есть противник) ===
<?php if($battleEnemy !== null): ?>
const attackBtn = document.getElementById('attack-btn');
if(attackBtn) {
    attackBtn.addEventListener('click', function() {
        const attack = document.querySelector('input[name="attack"]:checked').value;
        const defence = document.querySelector('input[name="defence"]:checked').value;
        
        if(!enemyId) {
            alert('Противник не найден');
            return;
        }
        
        const url = '<?= Url::to(['/battle/attack']) ?>?id=' + enemyId + '&defence=' + defence + '&attack=' + attack;
        sendAction(url, attackBtn);
    });
}

// === Обработчики для скиллов ===
document.querySelectorAll('.skill-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const skill = this.getAttribute('data-skill');
        const defence = document.querySelector('input[name="defence"]:checked').value;
        
        if(!enemyId) {
            alert('Противник не найден');
            return;
        }
        
        const url = '<?= Url::to(['/battle/skill']) ?>?id=' + enemyId + '&defence=' + defence + '&skill=' + skill;
        sendAction(url, this);
    });
});
<?php endif; ?>

// === Кнопка обновления (если ожидаем противника) ===
const reloadBtn = document.getElementById('reload-btn');
if(reloadBtn) {
    reloadBtn.addEventListener('click', function() {
        reloadPage();
    });
}

console.log('Battle page loaded');
console.log('Enemy ID:', enemyId);
</script>

<script>
    const weblog = document.getElementById('weblog');

    function addMessage(text, type = 'system') {
        //const div = document.createElement('div');
        // div.className = `msg ${type}`;
        // div.textContent = text;
        // weblog.appendChild(div);
        // weblog.scrollTop = log.scrollHeight;
    }

    // Получаем userId из PHP
    const userId = '<?php echo $user->session_id;?>';

    function connect() {
        const ws = new WebSocket(`ws://<?php echo Yii::$app->params['sockhost'];?>:8080/ws?user=${userId}`);

        ws.onopen = () => {
            addMessage(`✅ Соединение установлено. Ваш ID: ${userId}`, 'system');
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);

            switch(data.type) {
                case 'command':
                    if (data.command === 'reload') {
                        addMessage('🔄 Перезагрузка страницы...', 'reload');
                        location.reload(true);
                    }
                    break;

                default:
                    addMessage(`📩 От сервера: ${JSON.stringify(data)}`, 'server');
            }
        };

        ws.onclose = () => {
            addMessage('❌ Соединение закрыто. Пытаемся переподключиться...', 'system');
            setTimeout(connect, 3000);
        };

        ws.onerror = () => {
            addMessage('⚠ Ошибка соединения.', 'system');
        };
    }

    connect();
</script>