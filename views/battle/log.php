<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>

<style>
.log-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.log-header {
    text-align: center;
    margin-bottom: 30px;
}

.log-header h1 {
    color: #ffd27b;
    text-shadow: 2px 2px 4px #000;
    font-size: 32px;
    margin-bottom: 10px;
}

.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #ffd27b;
    text-decoration: none;
    font-size: 16px;
}

.back-link:hover {
    text-decoration: underline;
}

.battle-info {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: center;
}

.battle-info h2 {
    color: #ffd27b;
    font-size: 20px;
    margin-bottom: 10px;
}

.battle-id {
    color: #b89a6a;
    font-size: 14px;
}

/* Фильтр */
.filter-form {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 20px;
}

.filter-form h3 {
    color: #ffd27b;
    margin-bottom: 15px;
    font-size: 18px;
}

.filter-input-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-select {
    flex: 1;
    padding: 10px 15px;
    background: #22160f;
    border: 1px solid #7b5a2f;
    border-radius: 12px;
    color: #ffd27b;
    font-size: 14px;
    cursor: pointer;
}

.filter-select option {
    background: #22160f;
    color: #d8c08a;
}

.filter-select:focus {
    outline: none;
    border-color: #d2a45b;
}

.filter-btn {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    border: 2px solid #d2a45b;
    border-radius: 12px;
    color: #fff1cc;
    padding: 10px 25px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn:hover {
    background: linear-gradient(to bottom, #a56a2b, #6a3d18);
    transform: scale(1.02);
}

.clear-btn {
    background: linear-gradient(to bottom, #5a3a2a, #3a2218);
    border: 2px solid #8b5a3a;
}

.clear-btn:hover {
    background: linear-gradient(to bottom, #7a4a3a, #4a2a1a);
}

/* Участники */
.participants {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 15px;
    margin-bottom: 20px;
}

.participants h3 {
    color: #ffd27b;
    margin-bottom: 15px;
    font-size: 18px;
}

.participants-grid {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.team-participants {
    flex: 1;
    text-align: center;
}

.team-participants h4 {
    color: #ffaa66;
    margin-bottom: 10px;
    font-size: 16px;
}

.participant-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.participant-item {
    background: rgba(0,0,0,0.3);
    border-radius: 10px;
    padding: 8px;
    font-size: 14px;
}

.participant-name {
    color: #ffd27b;
    font-weight: bold;
}

.participant-name a {
    color: #ffd27b;
    text-decoration: none;
}

.participant-name a:hover {
    text-decoration: underline;
}

.participant-damage {
    color: #ffaa66;
    font-size: 12px;
    margin-top: 4px;
}

/* Логи */
.logs-list {
    background: linear-gradient(to bottom, #1a0f08, #0f0805);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 20px;
}

.logs-list h3 {
    color: #ffd27b;
    margin-bottom: 15px;
    font-size: 18px;
}

.logs-table {
    width: 100%;
    border-collapse: collapse;
}

.logs-table th {
    text-align: left;
    padding: 10px;
    color: #ffd27b;
    border-bottom: 1px solid #7b5a2f;
    font-weight: bold;
}

.logs-table td {
    padding: 10px;
    border-bottom: 1px solid #3a2817;
    color: #d8c08a;
    font-size: 13px;
    font-family: monospace;
}

.log-time {
    color: #b89a6a;
    white-space: nowrap;
    width: 80px;
}

.log-message {
    word-break: break-word;
}

.log-message.critical {
    color: #ff6666;
    font-weight: bold;
}

.log-message.skill {
    color: #88ff88;
}

.log-message.damage {
    color: #ffaa88;
}

.log-message.system {
    color: #88aaff;
}

.empty-message {
    text-align: center;
    padding: 40px;
    color: #b89a6a;
    font-size: 16px;
}

.pagination-container {
    margin-top: 20px;
    text-align: center;
}

.pagination-container .pagination {
    display: inline-flex;
    gap: 5px;
    list-style: none;
    padding: 0;
}

.pagination-container .pagination li a,
.pagination-container .pagination li span {
    background: linear-gradient(to bottom, #3a2817, #21150d);
    border: 1px solid #7b5a2f;
    border-radius: 8px;
    color: #d8c08a;
    padding: 6px 12px;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-container .pagination li.active span {
    background: linear-gradient(to bottom, #5b3a1d, #3a2210);
    color: #ffd27b;
    border-color: #d2a45b;
}

.pagination-container .pagination li a:hover {
    background: linear-gradient(to bottom, #4c3218, #2a1a0e);
    color: #ffd27b;
    transform: scale(1.02);
}
</style>

<div class="log-container">
    <div class="log-header">
        <a href="<?= Url::to(['/battle/history']) ?>" class="back-link">← Вернуться к истории</a>
        <h1>📜 Лог боя #<?= $battleId ?></h1>
    </div>
    
    <!-- Информация о битве -->
    <div class="battle-info">
        <h2>⚔️ Поединок #<?= $battleId ?></h2>
        <div class="battle-id">Всего записей: <?= $pagination ? $pagination->totalCount : 0 ?></div>
    </div>
    
    <!-- Фильтр по игроку -->
    <div class="filter-form">
        <h3>🔍 Фильтр по игроку</h3>
        <form method="get" action="<?= Url::to(['/battle/log']) ?>">
            <!-- ОБЯЗАТЕЛЬНО добавляем скрытое поле с id -->
            <input type="hidden" name="id" value="<?= $battleId ?>">
            <div class="filter-input-group">
                <select name="username" class="filter-select">
                    <option value="">-- Все участники --</option>
                    <?php foreach($players as $player): ?>
                        <option value="<?= Html::encode($player['name']) ?>" <?= $filterUsername == $player['name'] ? 'selected' : '' ?>>
                            <?= Html::encode($player['name']) ?> (Команда <?= $player['komand'] ?>, 💥 Урон: <?= $player['damage'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="filter-btn">🔍 Показать</button>
                <?php if($filterUsername): ?>
                    <a href="<?= Url::to(['/battle/log', 'id' => $battleId]) ?>" class="filter-btn clear-btn">❌ Сбросить</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Участники битвы -->
    <div class="participants">
        <h3>👥 Участники битвы</h3>
        <div class="participants-grid">
            <div class="team-participants">
                <h4>Команда 1</h4>
                <div class="participant-list">
                    <?php 
                        $team1Players = array_filter($players, function($p) { return $p['komand'] == 1; });
                        foreach($team1Players as $player): 
                    ?>
                        <div class="participant-item">
                            <div class="participant-name">
                                <?php if($player['type'] == 'bot'): ?>
                                    🤖 <?= Html::encode($player['name']) ?>
                                <?php else: ?>
                                    <a href="<?= Url::to(['/site/info', 'username' => $player['name']]) ?>" target="_blank">
                                        👤 <?= Html::encode($player['name']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="participant-damage">
                                💥 Урон: <?= $player['damage'] ?> | ❤️ HP: <?= $player['hp'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="team-participants">
                <h4>Команда 2</h4>
                <div class="participant-list">
                    <?php 
                        $team2Players = array_filter($players, function($p) { return $p['komand'] == 2; });
                        foreach($team2Players as $player): 
                    ?>
                        <div class="participant-item">
                            <div class="participant-name">
                                <?php if($player['type'] == 'bot'): ?>
                                    🤖 <?= Html::encode($player['name']) ?>
                                <?php else: ?>
                                    <a href="<?= Url::to(['/site/info', 'username' => $player['name']]) ?>" target="_blank">
                                        👤 <?= Html::encode($player['name']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="participant-damage">
                                💥 Урон: <?= $player['damage'] ?> | ❤️ HP: <?= $player['hp'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Логи битвы -->
    <div class="logs-list">
        <h3>📋 Хронология боя</h3>
        
        <?php if(!empty($logs)): ?>
            <table class="logs-table">
                <thead>
                    <tr>
                        <th class="log-time">⏱️ Время</th>
                        <th class="log-message">📝 Событие</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): 
                        $logClass = '';
                        if(strpos($log->log, 'КРИТ') !== false) {
                            $logClass = 'critical';
                        } elseif(strpos($log->log, 'использовал') !== false) {
                            $logClass = 'skill';
                        } elseif(strpos($log->log, 'нанес') !== false) {
                            $logClass = 'damage';
                        } else {
                            $logClass = 'system';
                        }
                    ?>
                        <tr>
                            <td class="log-time"><?= date('H:i:s', $log->attack_time) ?></td>
                            <td class="log-message <?= $logClass ?>"><?= $log->log ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Пагинация -->
            <?php if($pagination && $pagination->pageCount > 1): ?>
                <div class="pagination-container">
                    <?= LinkPager::widget([
                        'pagination' => $pagination,
                        'options' => ['class' => 'pagination'],
                        'linkOptions' => ['class' => 'page-link'],
                        'activePageCssClass' => 'active',
                        'disabledPageCssClass' => 'disabled',
                        'prevPageLabel' => '←',
                        'nextPageLabel' => '→',
                    ]) ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-message">
                📭 Нет записей в логе боя
            </div>
        <?php endif; ?>
    </div>
</div>