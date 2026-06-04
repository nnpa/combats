<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = $clan->name;
?>

<div class="clan-container">
    <div class="clan-header">
        <div class="clan-header-info">
            <div class="clan-icon-large">
                <img src="<?= $clan->img ?>" alt="<?= Html::encode($clan->name) ?>">
            </div>
            <div class="clan-header-text">
                <h1><?= Html::encode($clan->name) ?></h1>
                <p class="subtitle">Глава: <?= Html::encode($clan->admin->username ?? 'Неизвестен') ?></p>
            </div>
        </div>
        <div class="clan-header-actions">
            <?php if (!$isLeader): ?>
                <a href="/clan/leave" class="btn-leave" onclick="return confirm('Вы уверены, что хотите покинуть клан?')">Покинуть клан</a>
            <?php else: ?>
                <a href="/clan/disband" class="btn-disband" onclick="return confirm('ВНИМАНИЕ! Клан будет распущен навсегда. Все участники будут исключены. Продолжить?')">Распустить клан</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="clan-layout">
        <!-- Участники клана -->
        <div class="clan-members-section">
            <div class="section-title">
                <h2>Участники клана (<?= count($members) ?>)</h2>
            </div>
            
            <div class="members-list">
                <?php foreach ($members as $member): ?>
                    <div class="member-card">
                        <div class="member-info">
                            <div class="member-name"><?= Html::encode($member->user->username ?? 'Неизвестен') ?></div>
                            <div class="member-description"><?= Html::encode($member->description ?: 'Нет описания') ?></div>
                        </div>
                        <?php if ($isLeader && $member->user_id != Yii::$app->user->id): ?>
                            <div class="member-actions">
                                <button class="btn-edit-desc" data-id="<?= $member->id ?>" data-desc="<?= Html::encode($member->description) ?>">✏️ Описание</button>
                                <a href="/clan/kick-member?id=<?= $member->id ?>" class="btn-kick" onclick="return confirm('Исключить игрока из клана?')">Исключить</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Заявки (только для главы) -->
        <?php if ($isLeader && !empty($requests)): ?>
            <div class="clan-requests-section">
                <div class="section-title">
                    <h2>Заявки на вступление (<?= count($requests) ?>)</h2>
                </div>
                
                <div class="requests-list">
                    <?php foreach ($requests as $request): ?>
                        <div class="request-card">
                            <div class="request-info">
                                <div class="request-name"><?= Html::encode($request->user->username ?? 'Неизвестен') ?></div>
                                <div class="request-time">Заявка от: <?= Yii::$app->formatter->asDatetime($request->created_at) ?></div>
                            </div>
                            <div class="request-actions">
                                <a href="/clan/accept-request?id=<?= $request->id ?>" class="btn-accept">Принять</a>
                                <a href="/clan/reject-request?id=<?= $request->id ?>" class="btn-reject" onclick="return confirm('Отклонить заявку?')">Отклонить</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно для изменения описания -->
<div id="descModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Изменить описание игрока</h3>
            <span class="modal-close">&times;</span>
        </div>
        <form id="descForm" action="/clan/update-description" method="post">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input type="hidden" name="id" id="descId" value="">
            <div class="form-group">
                <label>Описание</label>
                <input type="text" name="description" id="descText" class="form-input" maxlength="100">
            </div>
            <button type="submit" class="btn-save">Сохранить</button>
        </form>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.clan-container {
    width: 100%;
    max-width: 100%;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
}

.clan-header {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    border: 1px solid #5c3a2a;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.clan-header-info {
    display: flex;
    align-items: center;
    gap: 20px;
}

.clan-icon-large {
    width: 80px;
    height: 80px;
}

.clan-icon-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
}

.clan-header-text h1 {
    color: #ffd700;
    font-size: 28px;
    margin-bottom: 5px;
}

.subtitle {
    color: #c9a87b;
}

.btn-leave, .btn-disband {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
}

.btn-leave {
    background: #6b2d2d;
    color: #ff6666;
}

.btn-leave:hover {
    background: #8b3d3d;
}

.btn-disband {
    background: #8b2d2d;
    color: #ff6666;
}

.btn-disband:hover {
    background: #ab4d4d;
}

.clan-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

@media (max-width: 900px) {
    .clan-layout {
        grid-template-columns: 1fr;
    }
}

.clan-members-section, .clan-requests-section {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #5c3a2a;
}

.section-title h2 {
    color: #ffd700;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #5c3a2a;
}

.members-list, .requests-list {
    max-height: 500px;
    overflow-y: auto;
}

.member-card, .request-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #2c1810;
    border-radius: 8px;
    margin-bottom: 10px;
}

.member-card:hover, .request-card:hover {
    background: #4a2e1f;
}

.member-name, .request-name {
    color: #ffd700;
    font-weight: bold;
}

.member-description {
    color: #c9a87b;
    font-size: 12px;
    margin-top: 4px;
}

.request-time {
    color: #8b7355;
    font-size: 11px;
    margin-top: 4px;
}

.member-actions, .request-actions {
    display: flex;
    gap: 10px;
}

.btn-edit-desc {
    background: #8b5e3c;
    color: #ffd700;
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
}

.btn-kick {
    background: #6b2d2d;
    color: #ff6666;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
}

.btn-accept {
    background: #2d6b2d;
    color: #66ff66;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
}

.btn-reject {
    background: #6b2d2d;
    color: #ff6666;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
}

/* Модальное окно */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background: #3d2317;
    margin: 15% auto;
    padding: 20px;
    width: 400px;
    border-radius: 12px;
    border: 1px solid #ffd700;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-header h3 {
    color: #ffd700;
}

.modal-close {
    color: #c9a87b;
    font-size: 28px;
    cursor: pointer;
}

.modal-close:hover {
    color: #ffd700;
}

.btn-save {
    width: 100%;
    background: #8b5e3c;
    color: #ffd700;
    padding: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
</style>

<script>
// Модальное окно для изменения описания
var modal = document.getElementById('descModal');
var closeBtn = document.getElementsByClassName('modal-close')[0];

document.querySelectorAll('.btn-edit-desc').forEach(function(btn) {
    btn.onclick = function() {
        var id = this.getAttribute('data-id');
        var desc = this.getAttribute('data-desc');
        document.getElementById('descId').value = id;
        document.getElementById('descText').value = desc;
        modal.style.display = 'block';
    };
});

if (closeBtn) {
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
};
</script>