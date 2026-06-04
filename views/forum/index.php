<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Форум';
?>

<div class="forum-container">
    <div class="forum-header">
        <div class="header-left">
            <h1>Форум</h1>
            <p class="subtitle">Обсуждения и вопросы</p>
        </div>
        <div class="header-right">
            <?= Html::a('➕ Создать тему', ['create'], ['class' => 'btn-create']) ?>
        </div>
    </div>

    <div class="forum-stats">
        <div class="stat-item">
            <span class="stat-icon">📊</span>
            <span class="stat-text">Всего тем: <?= $pagination->totalCount ?></span>
        </div>
    </div>

    <?php if (empty($topics)): ?>
        <div class="empty-message">
            <p>📭 Пока нет ни одной темы. Будьте первым!</p>
            <?= Html::a('➕ Создать тему', ['create'], ['class' => 'btn-create-empty']) ?>
        </div>
    <?php else: ?>
        <div class="topics-list">
            <div class="topics-header">
                <div class="col-topic">Тема</div>
                <div class="col-author">Автор</div>
                <div class="col-replies">Ответы</div>
                <div class="col-views">Просмотры</div>
                <div class="col-last">Последнее сообщение</div>
                <div class="col-actions"></div>
            </div>
            
            <?php foreach ($topics as $topic): ?>
                <div class="topic-row">
                    <div class="col-topic">
                        <?= Html::a(Html::encode($topic->title), ['view', 'id' => $topic->id], ['class' => 'topic-title']) ?>
                    </div>
                    <div class="col-author">
                        <span class="author-name"><?= Html::encode($topic->user->username ?? 'Удален') ?></span>
                        <span class="author-time"><?= Yii::$app->formatter->asRelativeTime($topic->created_at) ?></span>
                    </div>
                    <div class="col-replies"><?= $topic->replies_count ?></div>
                    <div class="col-views"><?= $topic->views ?></div>
                    <div class="col-last">
                        <?php if ($topic->lastReply): ?>
                            <span class="last-author"><?= Html::encode($topic->lastReply->user->username ?? 'Удален') ?></span>
                            <span class="last-time"><?= Yii::$app->formatter->asRelativeTime($topic->last_reply_time) ?></span>
                        <?php else: ?>
                            <span class="no-replies">Нет ответов</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-actions">
                        <?php $currentUser = Yii::$app->user; ?>
                        <?php $isAdmin = ($currentUser->identity && $currentUser->identity->username == 'Admin'); ?>
                        <?php if ($topic->user_id == $currentUser->id || $isAdmin): ?>
                            <?= Html::a('🗑️', ['delete', 'id' => $topic->id], [
                                'class' => 'delete-topic',
                                'data-confirm' => 'Вы уверены, что хотите удалить эту тему? Все ответы также будут удалены.',
                            ]) ?>
                        <?php endif; ?>
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

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.forum-container {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #2c1810;
    min-height: 100vh;
}

.forum-header {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px 30px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    border: 1px solid #5c3a2a;
}

.header-left h1 {
    color: #ffd700;
    font-size: 28px;
    margin-bottom: 5px;
}

.subtitle {
    color: #c9a87b;
    font-size: 14px;
}

.btn-create {
    background: #8b5e3c;
    color: #ffd700;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-create:hover {
    background: #a0744f;
    transform: scale(1.02);
}

.forum-stats {
    background: #3d2317;
    border-radius: 8px;
    padding: 10px 20px;
    margin-bottom: 20px;
    border: 1px solid #5c3a2a;
}

.stat-item {
    display: inline-block;
    margin-right: 20px;
    color: #c9a87b;
    font-size: 14px;
}

.topics-list {
    background: #3d2317;
    border-radius: 12px;
    overflow-x: auto;
    border: 1px solid #5c3a2a;
}

.topics-header {
    display: grid;
    grid-template-columns: 3fr 1.5fr 0.8fr 0.8fr 1.5fr 0.5fr;
    background: #2c1810;
    padding: 12px 15px;
    border-bottom: 1px solid #5c3a2a;
    color: #c9a87b;
    font-size: 13px;
    font-weight: bold;
    min-width: 800px;
}

.topic-row {
    display: grid;
    grid-template-columns: 3fr 1.5fr 0.8fr 0.8fr 1.5fr 0.5fr;
    padding: 15px;
    border-bottom: 1px solid #5c3a2a;
    align-items: center;
    min-width: 800px;
    transition: background 0.2s;
}

.topic-row:hover {
    background: #4a2e1f;
}

.topic-title {
    color: #ffd700;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
}

.topic-title:hover {
    text-decoration: underline;
}

.author-name, .last-author {
    color: #c9a87b;
    font-size: 13px;
    display: block;
}

.author-time, .last-time {
    color: #8b7355;
    font-size: 11px;
    display: block;
    margin-top: 2px;
}

.no-replies {
    color: #8b7355;
    font-size: 12px;
}

.col-replies, .col-views {
    color: #c9a87b;
    font-size: 14px;
    text-align: center;
}

.delete-topic {
    color: #ff6666;
    text-decoration: none;
    font-size: 18px;
    transition: color 0.2s;
}

.delete-topic:hover {
    color: #ff9999;
}

.empty-message {
    text-align: center;
    padding: 50px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
    color: #c9a87b;
}

.btn-create-empty {
    display: inline-block;
    margin-top: 15px;
    background: #8b5e3c;
    color: #ffd700;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
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
    flex-wrap: wrap;
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

.pagination li a:hover {
    background: #8b5e3c;
    color: #ffd700;
    border-color: #ffd700;
}

.pagination .active span {
    background: #8b5e3c;
    color: #ffd700;
    border-color: #ffd700;
}

@media (max-width: 900px) {
    .forum-container {
        padding: 10px;
    }
    
    .forum-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>