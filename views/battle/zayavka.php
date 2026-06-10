<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>

<style>
.battle-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.battle-header {
    text-align: center;
    margin-bottom: 30px;
}

.battle-header h1 {
    color: #ffd27b;
    text-shadow: 2px 2px 4px #000;
    font-size: 32px;
    margin: 0 0 15px 0;
}

.history-link {
    text-align: center;
    margin-bottom: 20px;
}

.history-btn {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    border: 2px solid #d2a45b;
    border-radius: 12px;
    color: #fff1cc;
    padding: 8px 25px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.history-btn:hover {
    background: linear-gradient(to bottom, #a56a2b, #6a3d18);
    transform: scale(1.05);
    color: #ffd27b;
}

/* Создание заявки */
.create-battle {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 30px;
}

.create-battle h2 {
    color: #ffd27b;
    margin-bottom: 15px;
    font-size: 24px;
}

.battle-type-buttons {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.battle-type-btn {
    background: linear-gradient(to bottom, #3a2817, #21150d);
    border: 2px solid #7b5a2f;
    border-radius: 12px;
    color: #d8c08a;
    padding: 12px 30px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
}

.battle-type-btn:hover {
    background: linear-gradient(to bottom, #4c3218, #2a1a0e);
    color: #ffd27b;
    transform: scale(1.05);
}

.battle-type-btn.selected {
    background: linear-gradient(to bottom, #5b3a1d, #3a2210);
    color: #ffd27b;
    border-color: #d2a45b;
}

.submit-battle {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    border: 2px solid #d2a45b;
    border-radius: 14px;
    color: #fff1cc;
    padding: 10px 30px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 20px;
    transition: all 0.2s ease;
}

.submit-battle:hover {
    background: linear-gradient(to bottom, #a56a2b, #6a3d18);
    transform: scale(1.05);
}

/* Список заявок */
.battles-list {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 20px;
}

.battles-list h2 {
    color: #ffd27b;
    margin-bottom: 20px;
    font-size: 24px;
}

.battle-card {
    background: linear-gradient(to bottom, #1f150e, #140e09);
    border: 1px solid #7b5a2f;
    border-radius: 14px;
    padding: 15px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
}

.battle-card:hover {
    transform: translateX(5px);
    border-color: #d2a45b;
}

.battle-info {
    flex: 1;
}

.battle-creator {
    font-size: 18px;
    font-weight: bold;
    color: #ffd27b;
    margin-bottom: 5px;
}

.battle-details {
    display: flex;
    gap: 20px;
    color: #b89a6a;
    font-size: 14px;
    flex-wrap: wrap;
}

.battle-type {
    background: #5b361d;
    padding: 2px 8px;
    border-radius: 10px;
    color: #ffcb8a;
}

.battle-timer {
    color: #ffaa66;
    font-weight: bold;
}

.join-button {
    background: linear-gradient(to bottom, #2d6a2d, #1a4a1a);
    border: 1px solid #5a9e5a;
    border-radius: 10px;
    color: #ccffcc;
    padding: 8px 20px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
}

.join-button:hover {
    background: linear-gradient(to bottom, #3d8a3d, #2a5a2a);
    transform: scale(1.05);
}

.join-button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.empty-message {
    text-align: center;
    padding: 40px;
    color: #b89a6a;
    font-size: 16px;
}

/* Сообщение об ожидании */
.waiting-message {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #ffd27b;
    border-radius: 18px;
    padding: 30px;
    text-align: center;
    margin-bottom: 30px;
}

.waiting-message h2 {
    color: #ffd27b;
    margin-bottom: 15px;
}

.waiting-message p {
    color: #d8c08a;
    font-size: 16px;
    margin-bottom: 10px;
}

.waiting-timer {
    font-size: 24px;
    color: #ffaa66;
    font-weight: bold;
    margin: 15px 0;
}

.waiting-note {
    color: #b89a6a;
    font-size: 12px;
    margin-top: 15px;
}
</style>

<div class="battle-container">
    <div class="battle-header">
        <h1>⚔️ Арена Поединков ⚔️</h1>
        <div class="history-link">
            <a href="<?= Url::to(['/battle/history']) ?>" class="history-btn" target="_blank">📜 История поединков</a>
        </div>
    </div>

    <?php if($user->battle_id !== null): ?>
        <!-- Если пользователь уже в заявке -->
        <?php
            $currentBattle = \app\models\Battle::findOne(['id' => $user->battle_id]);
            if($currentBattle && $currentBattle->started === null):
        ?>
        <div class="waiting-message">
            <h2>⏳ Ожидание соперников...</h2>
            <p>Вы создали заявку на бой <strong><?= $currentBattle->type == 1 ? '1x1' : '3x3' ?></strong></p>
            <p>Максимум участников: <strong><?= $currentBattle->type == 1 ? '2' : '6' ?></strong></p>
            <p>Если не наберется нужное количество, бой начнется с ботом через:</p>
            <div class="waiting-timer" id="battle-timer" data-start-time="<?= $currentBattle->start_time ?>">
                <?php
                    $remaining = $currentBattle->start_time - time();
                    if($remaining > 0){
                        $minutes = floor($remaining / 60);
                        $seconds = $remaining % 60;
                        echo $minutes . ':' . ($seconds < 10 ? '0' . $seconds : $seconds);
                    } else {
                        echo '0:00';
                    }
                ?>
            </div>
            <div class="waiting-note">
                ℹ️ Примечание: Вы не можете отменить заявку. Если не наберется нужное количество игроков, недостающие места будут заполнены ботами.
            </div>
        </div>
        <?php else: ?>
            <script>window.location.href = '<?= Url::to(['/battle/battle']) ?>';</script>
        <?php endif; ?>
    <?php else: ?>
        <!-- Форма создания заявки -->
        <div class="create-battle">
            <h2>📝 Создать заявку на бой</h2>
            <form id="battle-form" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <p style="color: #d8c08a; margin-bottom: 10px;">Выберите формат боя:</p>
                <div class="battle-type-buttons">
                    <button type="button" class="battle-type-btn" data-type="1">⚔️ 1 на 1</button>
                    <button type="button" class="battle-type-btn" data-type="3">👥 3 на 3</button>
                </div>
                <input type="hidden" name="count" id="battle-type-input" value="1">
                <div style="margin-top: 20px;">
                    <button type="submit" class="submit-battle">🚀 Создать заявку</button>
                </div>
            </form>
        </div>

        <!-- Список доступных заявок -->
        <div class="battles-list">
            <h2>📋 Доступные заявки</h2>
            <?php if(!empty($battles)): ?>
                <?php foreach($battles as $battle): 
                    $creator = \app\models\UserBattle::find()
                        ->where(['battle_id' => $battle->id])
                        ->one();
                    $creatorName = 'Неизвестный';
                    if($creator && $creator->user_id){
                        $userModel = \app\models\User::findOne($creator->user_id);
                        $creatorName = $userModel ? $userModel->username : 'Неизвестный';
                    }
                    
                    $participantsCount = \app\models\UserBattle::find()
                        ->where(['battle_id' => $battle->id])
                        ->count();
                    
                    // Правильный расчет максимального количества игроков
                    // Для type=1 -> 2 игрока (1x1)
                    // Для type=3 -> 6 игроков (3x3)
                    $maxPlayers = $battle->type * 2;
                    $isFull = $participantsCount >= $maxPlayers;
                    
                    // Проверяем, не участвует ли уже пользователь в этой битве
                    $alreadyInBattle = \app\models\UserBattle::find()
                        ->where(['battle_id' => $battle->id, 'user_id' => $user->id])
                        ->exists();
                ?>
                <div class="battle-card" data-battle-id="<?= $battle->id ?>">
                    <div class="battle-info">
                        <div class="battle-creator">👤 Создатель: <?= Html::encode($creatorName) ?></div>
                        <div class="battle-details">
                            <span class="battle-type"><?= $battle->type == 1 ? '⚔️ 1x1' : '👥 3x3' ?></span>
                            <span>Уровень: <?= $battle->level ?></span>
                            <span>Участников: <span class="participants-count"><?= $participantsCount ?></span>/<?= $maxPlayers ?></span>
                            <span class="battle-timer" data-start-time="<?= $battle->start_time ?>">
                                ⏱️ Начало через: 
                                <?php
                                    $remaining = $battle->start_time - time();
                                    if($remaining > 0){
                                        $minutes = floor($remaining / 60);
                                        $seconds = $remaining % 60;
                                        echo $minutes . ':' . ($seconds < 10 ? '0' . $seconds : $seconds);
                                    } else {
                                        echo '0:00';
                                    }
                                ?>
                            </span>
                        </div>
                    </div>
                    <?php if(!$isFull && !$alreadyInBattle): ?>
                        <button class="join-button" onclick="joinBattle(<?= $battle->id ?>)">🎮 Присоединиться</button>
                    <?php elseif($alreadyInBattle): ?>
                        <button class="join-button" style="opacity:0.5; cursor:not-allowed;" disabled>✅ Вы уже в этом бою</button>
                    <?php else: ?>
                        <button class="join-button" style="opacity:0.5; cursor:not-allowed;" disabled>🙅 Мест нет</button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-message" id="empty-message">
                    📭 Нет активных заявок. Создайте свою!
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Выбор типа боя
const typeBtns = document.querySelectorAll('.battle-type-btn');
const typeInput = document.getElementById('battle-type-input');

if(typeBtns.length > 0) {
    typeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            typeBtns.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            typeInput.value = this.getAttribute('data-type');
        });
    });
    typeBtns[0].classList.add('selected');
}

// Функция присоединения к бою
function joinBattle(battleId) {
    if(confirm('Присоединиться к этому бою?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= Url::to(['/battle/index']) ?>';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= Yii::$app->request->csrfParam ?>';
        csrfInput.value = '<?= Yii::$app->request->csrfToken ?>';
        form.appendChild(csrfInput);
        
        const battleInput = document.createElement('input');
        battleInput.type = 'hidden';
        battleInput.name = 'battle_id';
        battleInput.value = battleId;
        form.appendChild(battleInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Обновление таймеров
function updateTimers() {
    const timers = document.querySelectorAll('.battle-timer, #battle-timer');
    const now = Math.floor(Date.now() / 1000);
    
    timers.forEach(timer => {
        const startTime = parseInt(timer.getAttribute('data-start-time'));
        if(startTime && startTime > now) {
            const remaining = startTime - now;
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            const timeStr = minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
            
            if(timer.id === 'battle-timer') {
                timer.textContent = timeStr;
            } else {
                const text = timer.textContent;
                const prefix = text.split('через:')[0];
                timer.textContent = prefix + 'через: ' + timeStr;
            }
        } else if(startTime && startTime <= now) {
            // Если время вышло, перезагружаем страницу
            location.reload();
        }
    });
}

// Запускаем таймеры
if(document.querySelectorAll('.battle-timer, #battle-timer').length > 0) {
    updateTimers();
    setInterval(updateTimers, 1000);
}

// Автообновление списка заявок каждые 10 секунд (если пользователь не в бою)
<?php if($user->battle_id === null): ?>
setInterval(function() {
    location.reload();
}, 10000);
<?php endif; ?>
</script>