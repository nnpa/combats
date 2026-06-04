<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>

<style>
.history-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.history-header {
    text-align: center;
    margin-bottom: 30px;
}

.history-header h1 {
    color: #ffd27b;
    text-shadow: 2px 2px 4px #000;
    font-size: 32px;
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

/* Поисковая форма */
.search-form {
    background: linear-gradient(to bottom, #2b1d12, #17110c);
    border: 2px solid #7b5a2f;
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 30px;
}

.search-form h2 {
    color: #ffd27b;
    margin-bottom: 15px;
    font-size: 20px;
}

.search-input-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.search-input {
    flex: 1;
    padding: 10px 15px;
    background: #22160f;
    border: 1px solid #7b5a2f;
    border-radius: 12px;
    color: #ffd27b;
    font-size: 16px;
}

.search-input:focus {
    outline: none;
    border-color: #d2a45b;
}

.search-btn {
    background: linear-gradient(to bottom, #8b5a22, #5b3515);
    border: 2px solid #d2a45b;
    border-radius: 12px;
    color: #fff1cc;
    padding: 10px 25px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
}

.search-btn:hover {
    background: linear-gradient(to bottom, #a56a2b, #6a3d18);
    transform: scale(1.02);
}

/* Результаты поиска */
.results-info {
    background: rgba(0,0,0,0.4);
    border-radius: 12px;
    padding: 10px 15px;
    margin-bottom: 20px;
    color: #d8c08a;
    font-size: 14px;
}

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
    transition: all 0.2s ease;
}

.battle-card:hover {
    transform: translateX(5px);
    border-color: #d2a45b;
}

.battle-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
    gap: 10px;
}

.battle-id {
    color: #b89a6a;
    font-size: 12px;
}

.battle-result {
    font-size: 18px;
    font-weight: bold;
}

.battle-result.win {
    color: #88ff88;
}

.battle-result.lose {
    color: #ff8888;
}

.battle-date {
    color: #b89a6a;
    font-size: 12px;
}

.teams-compare {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.team-compare {
    flex: 1;
    text-align: center;
    background: rgba(0,0,0,0.3);
    border-radius: 12px;
    padding: 10px;
}

.team-compare.vs {
    flex: 0 0 auto;
    font-size: 20px;
    color: #ffd27b;
}

.team-compare h4 {
    color: #ffd27b;
    margin-bottom: 8px;
    font-size: 14px;
}

.team-players {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.team-player {
    font-size: 13px;
    color: #d8c08a;
}

.team-player a {
    color: #ffd27b;
    text-decoration: none;
}

.team-player a:hover {
    text-decoration: underline;
}

.battle-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #3a2817;
    flex-wrap: wrap;
    gap: 10px;
}

.damage-info {
    color: #ffaa66;
    font-size: 13px;
}

.log-link {
    background: linear-gradient(to bottom, #2d5a8a, #1a3a5a);
    border: 1px solid #5a9ece;
    border-radius: 10px;
    color: #fff1cc;
    padding: 6px 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    font-size: 13px;
}

.log-link:hover {
    background: linear-gradient(to bottom, #3d7aaa, #2a4a6a);
    transform: scale(1.02);
    color: #ffd27b;
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

<div class="history-container">
    <div class="history-header">
        <a href="<?= Url::to(['/battle/index']) ?>" class="back-link">← Вернуться к арене</a>
        <h1>📜 История поединков</h1>
    </div>
    
    <!-- Форма поиска -->
    <div class="search-form">
        <h2>🔍 Поиск по имени игрока</h2>
        <form method="get" action="<?= Url::to(['/battle/history']) ?>">
            <div class="search-input-group">
                <input type="text" name="username" class="search-input" placeholder="Введите ник игрока..." value="<?= Html::encode($searchUsername) ?>">
                <button type="submit" class="search-btn">🔍 Найти</button>
            </div>
        </form>
    </div>
    
    <?php if($user): ?>
        <div class="results-info">
            📊 Показаны бои игрока: <strong style="color: #ffd27b;"><?= Html::encode($user->username) ?></strong>
            (Всего боёв: <?= $pagination ? $pagination->totalCount : 0 ?>)
        </div>
    <?php endif; ?>
    
    <!-- Список боёв -->
    <div class="battles-list">
        <h2>⚔️ Список поединков</h2>
        
        <?php if(!empty($history)): ?>
            <?php foreach($history as $battle): ?>
                <div class="battle-card">
                    <div class="battle-header">
                        <span class="battle-id">#<?= $battle['battle_id'] ?></span>
                        <span class="battle-date">📅 <?= $battle['date'] ?></span>
                        <span class="battle-result <?= $battle['is_winner'] ? 'win' : 'lose' ?>">
                            <?= $battle['winner_sign'] ?> <?= $battle['is_winner'] ? 'ПОБЕДА' : 'ПОРАЖЕНИЕ' ?>
                        </span>
                    </div>
                    
                    <div class="teams-compare">
                        <div class="team-compare">
                            <h4>👥 Команда 1</h4>
                            <div class="team-players">
                                <?php if(!empty($battle['team1_players'])): ?>
                                    <?php foreach($battle['team1_players'] as $player): ?>
                                        <div class="team-player">
                                            <?php if($player['isBot']): ?>
                                                <?= Html::encode($player['name']) ?>
                                            <?php else: ?>
                                                <a href="<?= Url::to(['/site/info', 'username' => $player['name']]) ?>" target="_blank">
                                                    <?= Html::encode($player['name']) ?>
                                                </a>
                                            <?php endif; ?>
                                            (ур. <?= $player['level'] ?>)
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="team-player">Нет участников</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="team-compare vs">VS</div>
                        
                        <div class="team-compare">
                            <h4>👥 Команда 2</h4>
                            <div class="team-players">
                                <?php if(!empty($battle['team2_players'])): ?>
                                    <?php foreach($battle['team2_players'] as $player): ?>
                                        <div class="team-player">
                                            <?php if($player['isBot']): ?>
                                                <?= Html::encode($player['name']) ?>
                                            <?php else: ?>
                                                <a href="<?= Url::to(['/site/info', 'username' => $player['name']]) ?>" target="_blank">
                                                    <?= Html::encode($player['name']) ?>
                                                </a>
                                            <?php endif; ?>
                                            (ур. <?= $player['level'] ?>)
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="team-player">Нет участников</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="battle-footer">
                        <div class="damage-info">
                            💥 Нанесено урона: <strong><?= $battle['total_damage'] ?></strong>
                        </div>
                        <a href="<?= Url::to(['/battle/log', 'id' => $battle['battle_id']]) ?>" class="log-link" target="_blank">📋 Смотреть лог боя</a>
                    </div>
                </div>
            <?php endforeach; ?>
            
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
            
        <?php elseif($searchUsername): ?>
            <div class="empty-message">
                🔍 Игрок <strong><?= Html::encode($searchUsername) ?></strong> не найден или у него нет завершённых боёв.
            </div>
        <?php else: ?>
            <div class="empty-message">
                📭 Введите имя игрока и нажмите "Найти", чтобы увидеть историю его боёв.
            </div>
        <?php endif; ?>
    </div>
</div>