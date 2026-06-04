<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = Html::encode($topic->title);
?>

<div class="forum-container">
    <div class="forum-header">
        <div class="header-left">
            <h1><?= Html::encode($topic->title) ?></h1>
            <div class="topic-meta">
                <span class="meta-item">👤 Автор: <?= Html::encode($topic->user->username ?? 'Удален') ?></span>
                <span class="meta-item">📅 <?= Yii::$app->formatter->asDatetime($topic->created_at) ?></span>
                <span class="meta-item">👁️ Просмотров: <?= $topic->views ?></span>
                <span class="meta-item">💬 Ответов: <?= $topic->replies_count ?></span>
            </div>
        </div>
        <div class="header-right">
            <?= Html::a('◀ Назад к форуму', ['index'], ['class' => 'btn-back']) ?>
        </div>
    </div>

    <!-- Первое сообщение (тема) -->
    <div class="topic-post">
        <div class="post-header">
            <div class="post-author"><?= Html::encode($topic->user->username ?? 'Удален') ?></div>
            <div class="post-date"><?= Yii::$app->formatter->asDatetime($topic->created_at) ?></div>
        </div>
        <div class="post-content">
            <?= nl2br(Html::encode($topic->content)) ?>
        </div>
    </div>

    <!-- Ответы -->
    <?php if (!empty($replies)): ?>
        <div class="replies-section">
            <h2 class="replies-title">📝 Ответы (<?= $pagination->totalCount ?>)</h2>
            
            <?php foreach ($replies as $reply): ?>
                <div class="reply-post" id="reply-<?= $reply->id ?>">
                    <div class="post-header">
                        <div class="post-author"><?= Html::encode($reply->user->username ?? 'Удален') ?></div>
                        <div class="post-date"><?= Yii::$app->formatter->asDatetime($reply->created_at) ?></div>
                    </div>
                    <div class="post-content">
                        <?= nl2br(Html::encode($reply->content)) ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="pagination-container">
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'options' => ['class' => 'pagination'],
                    'nextPageLabel' => '→',
                    'prevPageLabel' => '←',
                ]) ?>
            </div>
        </div>
    <?php else: ?>
        <div class="no-replies-block">
            <p>📭 Пока нет ответов. Будьте первым!</p>
        </div>
    <?php endif; ?>

    <!-- Форма добавления ответа -->
    <div class="reply-form">
        <h2 class="reply-title">✏️ Добавить ответ</h2>
        
        <?php $form = ActiveForm::begin(['id' => 'reply-form']); ?>
        
        <div class="form-group">
            <?= $form->field($newReply, 'content')->textarea([
                'rows' => 8,
                'placeholder' => 'Введите ваш ответ...',
                'class' => 'form-textarea'
            ])->label(false) ?>
        </div>

        <div class="captcha-section">
            <div class="captcha-question">
                <span class="captcha-icon">🔒</span>
                <span class="captcha-text">Введите результат: <?= $captchaQuestion ?></span>
            </div>
            <div class="captcha-input-group">
                <input type="text" name="ForumReplies[captcha]" class="captcha-input" placeholder="Ваш ответ" autocomplete="off">
            </div>
        </div>

        <div class="form-buttons">
            <?= Html::submitButton('✏️ Отправить ответ', ['class' => 'btn-submit']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
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
    margin-bottom: 25px;
    border: 1px solid #5c3a2a;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 15px;
}

.header-left h1 {
    color: #ffd700;
    font-size: 24px;
    margin-bottom: 10px;
}

.topic-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.meta-item {
    color: #c9a87b;
    font-size: 13px;
}

.btn-back {
    background: #5c3a2a;
    color: #c9a87b;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
}

.btn-back:hover {
    background: #7a4f3a;
    color: #ffd700;
}

.topic-post, .reply-post {
    background: #3d2317;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid #5c3a2a;
    overflow: hidden;
}

.post-header {
    background: #2c1810;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    border-bottom: 1px solid #5c3a2a;
}

.post-author {
    color: #ffd700;
    font-weight: bold;
    font-size: 14px;
}

.post-date {
    color: #8b7355;
    font-size: 12px;
}

.post-content {
    padding: 20px;
    color: #e6c8a0;
    font-size: 14px;
    line-height: 1.5;
}

.replies-section {
    margin-top: 30px;
}

.replies-title, .reply-title {
    color: #ffd700;
    font-size: 18px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #5c3a2a;
}

.no-replies-block {
    text-align: center;
    padding: 30px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
    color: #c9a87b;
}

.reply-form {
    background: #3d2317;
    border-radius: 12px;
    padding: 25px;
    margin-top: 30px;
    border: 1px solid #5c3a2a;
}

.form-group {
    margin-bottom: 20px;
}

.form-textarea {
    width: 100%;
    padding: 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
    font-size: 14px;
    resize: vertical;
    font-family: inherit;
}

.form-textarea:focus {
    outline: none;
    border-color: #ffd700;
}

.captcha-section {
    background: #2c1810;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #5c3a2a;
}

.captcha-question {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.captcha-icon {
    font-size: 18px;
}

.captcha-text {
    color: #ffd700;
    font-size: 16px;
    font-weight: bold;
}

.captcha-input {
    width: 200px;
    padding: 10px;
    background: #3d2317;
    border: 1px solid #5c3a2a;
    border-radius: 6px;
    color: #ffd700;
    font-size: 14px;
}

.form-buttons {
    display: flex;
    gap: 15px;
}

.btn-submit {
    background: #8b5e3c;
    color: #ffd700;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-submit:hover {
    background: #a0744f;
    transform: scale(1.02);
}

.pagination-container {
    margin: 20px 0;
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
    padding: 6px 10px;
    background: #3d2317;
    border: 1px solid #5c3a2a;
    border-radius: 6px;
    color: #c9a87b;
    text-decoration: none;
    font-size: 13px;
}

.pagination li a:hover {
    background: #8b5e3c;
    color: #ffd700;
}

.pagination .active span {
    background: #8b5e3c;
    color: #ffd700;
}

@media (max-width: 768px) {
    .forum-container {
        padding: 10px;
    }
    
    .forum-header {
        flex-direction: column;
    }
    
    .post-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .captcha-input {
        width: 100%;
    }
    
    .form-buttons {
        flex-direction: column;
    }
}
</style>