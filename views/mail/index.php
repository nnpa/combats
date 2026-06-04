<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Почта';
?>

<div class="mail-container">
    <div class="mail-header">
        <h1>Почта</h1>
        <p class="subtitle">Отправка и получение предметов с наложенным платежом</p>
    </div>

    <div class="mail-layout">
        <!-- Левая колонка - Отправка письма -->
        <div class="mail-send-section">
            <div class="section-title">
                <h2>Отправить предмет</h2>
            </div>
            
            <div class="send-form">
                <div class="form-group">
                <div class="form-group">
                    <label>Получатель</label>
                    <input type="text" id="username" class="form-input" placeholder="Введите имя пользователя">
                    <button id="checkUserBtn" style="background:#ff6600; color:white; padding:12px 20px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; margin-top:10px; width:100%;">Проверить</button>
                    <div id="userCheckResult" class="check-result"></div>
                    <input type="hidden" id="to_user_id" value="">
                </div>

                <div class="form-group">
                    <label>Выберите предмет</label>
                    <div id="itemsList" class="items-list">
                        <div class="loading-items">Сначала выберите получателя</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Цена наложенного платежа (KR)</label>
                    <input type="number" id="price" class="form-input" placeholder="0" min="0" value="0">
                </div>

                <button id="sendMailBtn" class="btn-send" disabled>Отправить письмо</button>
            </div>
        </div>

        <!-- Правая колонка - Входящие письма -->
        <div class="mail-inbox-section">
            <div class="section-title">
                <h2>Входящие письма</h2>
            </div>
            
            <div class="inbox-list">
                <?php if (empty($incomingMails)): ?>
                    <div class="empty-inbox">Нет входящих писем</div>
                <?php else: ?>
                    <?php foreach ($incomingMails as $mail): ?>
                        <div class="mail-card">
                            <div class="mail-item-img">
                                <img src="/<?= $mail->item->img ?? 'img/default.png' ?>">
                            </div>
                            <div class="mail-item-info">
                                <div class="mail-item-name"><?= Html::encode($mail->item->name ?? 'Предмет удален') ?></div>
                                <div class="mail-item-from">От: <?= Html::encode($mail->fromUser->username ?? 'Неизвестно') ?></div>
                                <div class="mail-item-price">Цена: <?= number_format($mail->cost, 0, ',', ' ') ?> KR</div>
                            </div>
                            <div class="mail-item-actions">
                                <button class="btn-accept" data-id="<?= $mail->id ?>">Принять</button>
                                <button class="btn-reject" data-id="<?= $mail->id ?>">Отклонить</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Ждем полной загрузки страницы
document.addEventListener('DOMContentLoaded', function() {
    console.log('Страница загружена');
    
    var selectedItemId = null;
    
    // Проверка пользователя
    var checkBtn = document.getElementById('checkUserBtn');
    if (checkBtn) {
        console.log('Кнопка найдена');
        checkBtn.onclick = function() {
            var username = document.getElementById('username').value.trim();
            var resultDiv = document.getElementById('userCheckResult');
            var toUserId = document.getElementById('to_user_id');
            
            console.log('Проверка пользователя: ' + username);
            
            if (!username) {
                resultDiv.innerHTML = '<span class="error">Введите имя пользователя</span>';
                return;
            }
            
            resultDiv.innerHTML = '<span class="loading">Проверка...</span>';
            
            fetch('/mail/checkuser?username=' + encodeURIComponent(username))
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    console.log('Ответ:', data);
                    if (data.success) {
                        resultDiv.innerHTML = '<span class="success">Пользователь найден: ' + username + '</span>';
                        toUserId.value = data.user_id;
                        loadItems();
                    } else {
                        resultDiv.innerHTML = '<span class="error">' + data.message + '</span>';
                        toUserId.value = '';
                        document.getElementById('itemsList').innerHTML = '<div class="no-items">Сначала выберите получателя</div>';
                        selectedItemId = null;
                        checkSendButton();
                    }
                })
                .catch(function(error) {
                    console.error('Ошибка:', error);
                    resultDiv.innerHTML = '<span class="error">Ошибка сервера</span>';
                });
        };
    } else {
        console.log('Кнопка НЕ найдена!');
    }
    
    // Загрузка предметов
    function loadItems() {
        var itemsDiv = document.getElementById('itemsList');
        itemsDiv.innerHTML = '<div class="loading-items">Загрузка предметов...</div>';
        
        fetch('/mail/getitems')
            .then(function(response) {
                return response.json();
            })
            .then(function(items) {
                console.log('Предметы:', items);
                if (items.length === 0) {
                    itemsDiv.innerHTML = '<div class="no-items">Нет доступных предметов для отправки</div>';
                    return;
                }
                
                var html = '';
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    html += '<label class="item-radio">';
                    html += '<input type="radio" name="selected_item" value="' + item.id + '">';
                    html += '<img src="/' + item.img + '">';
                    html += '<span class="item-name">' + item.name + '</span>';
                    html += '<span class="item-price">Цена: ' + parseInt(item.cost).toLocaleString('ru-RU') + ' KR</span>';
                    html += '</label>';
                }
                itemsDiv.innerHTML = html;
                
                var radios = document.querySelectorAll('input[name="selected_item"]');
                for (var r = 0; r < radios.length; r++) {
                    radios[r].onchange = function() {
                        if (this.checked) {
                            selectedItemId = this.value;
                            checkSendButton();
                        }
                    };
                }
            })
            .catch(function(error) {
                console.error('Ошибка:', error);
                itemsDiv.innerHTML = '<div class="no-items">Ошибка загрузки предметов</div>';
            });
    }
    
    function checkSendButton() {
        var sendBtn = document.getElementById('sendMailBtn');
        var toUserId = document.getElementById('to_user_id').value;
        var price = document.getElementById('price').value;
        
        if (toUserId && selectedItemId && price !== '') {
            sendBtn.disabled = false;
        } else {
            sendBtn.disabled = true;
        }
    }
    
    // Отправка письма
    var sendBtn = document.getElementById('sendMailBtn');
    if (sendBtn) {
        sendBtn.onclick = function() {
            var username = document.getElementById('username').value.trim();
            var price = document.getElementById('price').value;
            var btn = this;
            
            if (!confirm('Отправить предмет получателю "' + username + '" за ' + parseInt(price).toLocaleString('ru-RU') + ' KR?')) {
                return;
            }
            
            btn.disabled = true;
            btn.textContent = 'Отправка...';
            
            fetch('/mail/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'to_username=' + encodeURIComponent(username) + '&item_id=' + selectedItemId + '&price=' + price
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                alert(data.message);
                if (data.success) {
                    location.reload();
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Отправить письмо';
                }
            });
        };
    }
    
    // Принять письмо
    var acceptBtns = document.querySelectorAll('.btn-accept');
    for (var a = 0; a < acceptBtns.length; a++) {
        acceptBtns[a].onclick = function() {
            var mailId = this.getAttribute('data-id');
            var btn = this;
            
            if (!confirm('Принять предмет? Деньги будут списаны с вашего счета.')) {
                return;
            }
            
            btn.disabled = true;
            btn.textContent = '...';
            
            fetch('/mail/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'mail_id=' + mailId
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                alert(data.message);
                if (data.success) {
                    location.reload();
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Принять';
                }
            });
        };
    }
    
    // Отклонить письмо
    var rejectBtns = document.querySelectorAll('.btn-reject');
    for (var r = 0; r < rejectBtns.length; r++) {
        rejectBtns[r].onclick = function() {
            var mailId = this.getAttribute('data-id');
            var btn = this;
            
            if (!confirm('Отклонить предмет? Он будет возвращен отправителю.')) {
                return;
            }
            
            btn.disabled = true;
            btn.textContent = '...';
            
            fetch('/mail/reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'mail_id=' + mailId
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                alert(data.message);
                if (data.success) {
                    location.reload();
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Отклонить';
                }
            });
        };
    }
    
    document.getElementById('price').oninput = function() {
        checkSendButton();
    };
    
    checkSendButton();
});
</script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.mail-container {
    width: 100%;
    max-width: 100%;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
}

.mail-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.mail-header h1 {
    color: #ffd700;
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
}

.mail-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

@media (max-width: 1000px) {
    .mail-layout {
        grid-template-columns: 1fr;
    }
}

.mail-send-section, .mail-inbox-section {
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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #ffd700;
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
}

.btn-check, .btn-send {
    background: #8b5e3c;
    color: #ffd700;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.btn-check:hover, .btn-send:hover:not(:disabled) {
    background: #a0744f;
}

.btn-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.check-result {
    margin-top: 10px;
}

.check-result .success { color: #66ff66; }
.check-result .error { color: #ff6666; }
.check-result .loading { color: #ffd700; }

.items-list {
    max-height: 300px;
    overflow-y: auto;
    background: #2c1810;
    border-radius: 8px;
    border: 1px solid #5c3a2a;
}

.item-radio {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #5c3a2a;
    cursor: pointer;
}

.item-radio:hover {
    background: #4a2e1f;
}

.item-radio input {
    margin-right: 10px;
}

.item-radio img {
    width: 40px;
    height: 40px;
    margin-right: 10px;
}

.item-radio .item-name {
    flex: 1;
    color: #ffd700;
}

.item-radio .item-price {
    color: #66ff66;
}

.loading-items, .no-items, .empty-inbox {
    text-align: center;
    padding: 30px;
    color: #c9a87b;
}

.mail-card {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #2c1810;
    border-radius: 10px;
    margin-bottom: 12px;
}

.mail-card:hover {
    background: #4a2e1f;
}

.mail-item-img {
    width: 50px;
    height: 50px;
    margin-right: 15px;
}

.mail-item-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.mail-item-info {
    flex: 1;
}

.mail-item-name {
    color: #ffd700;
    font-weight: bold;
}

.mail-item-from, .mail-item-price {
    color: #c9a87b;
    font-size: 12px;
}

.mail-item-actions {
    display: flex;
    gap: 10px;
}

.btn-accept, .btn-reject {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.btn-accept {
    background: #2d6b2d;
    color: #66ff66;
}

.btn-reject {
    background: #6b2d2d;
    color: #ff6666;
}

@media (max-width: 700px) {
    .mail-card {
        flex-direction: column;
        text-align: center;
    }
    .mail-item-img {
        margin-right: 0;
        margin-bottom: 10px;
    }
    .mail-item-actions {
        margin-top: 10px;
    }
}
.btn-check {
    background: #ff6600;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    margin-top: 10px;
    width: 100%;
    display: block;
    text-align: center;
}
</style>