<?php
use yii\helpers\Html;
use yii\helpers\Url;

$collapsed = $user->chat_collapsed ?? 0;
$height = $user->chat_height ?? 300;
?>

<div id="chat-widget" class="chat-widget" data-collapsed="<?= $collapsed ?>" style="height: <?= $collapsed ? '40px' : $height . 'px' ?>">
    <div class="chat-header" id="chat-header">
        <div class="chat-title">
            <span>💬 Общий чат</span>
            <span class="chat-online-count" id="onlineCount">0</span>
        </div>
        <div class="chat-controls">
            <button class="chat-minimize" id="chat-minimize"><?= $collapsed ? '▲' : '▼' ?></button>
        </div>
    </div>
    
    <div class="chat-body" id="chat-body" style="display: <?= $collapsed ? 'none' : 'flex' ?>">
        <div class="chat-main">
            <div class="chat-messages" id="chat-messages"></div>
            
            <div class="chat-input-area">
                <div class="smileys-panel" id="smileys-panel">
                    <button class="smiley-btn" data-smiley="😀">😀</button>
                    <button class="smiley-btn" data-smiley="😁">😁</button>
                    <button class="smiley-btn" data-smiley="😂">😂</button>
                    <button class="smiley-btn" data-smiley="😃">😃</button>
                    <button class="smiley-btn" data-smiley="😄">😄</button>
                    <button class="smiley-btn" data-smiley="😅">😅</button>
                    <button class="smiley-btn" data-smiley="😆">😆</button>
                    <button class="smiley-btn" data-smiley="😉">😉</button>
                    <button class="smiley-btn" data-smiley="😊">😊</button>
                    <button class="smiley-btn" data-smiley="😋">😋</button>
                    <button class="smiley-btn" data-smiley="😎">😎</button>
                    <button class="smiley-btn" data-smiley="😍">😍</button>
                    <button class="smiley-btn" data-smiley="😘">😘</button>
                    <button class="smiley-btn" data-smiley="😗">😗</button>
                    <button class="smiley-btn" data-smiley="😙">😙</button>
                    <button class="smiley-btn" data-smiley="😚">😚</button>
                    <button class="smiley-btn" data-smiley="🙂">🙂</button>
                    <button class="smiley-btn" data-smiley="🤗">🤗</button>
                    <button class="smiley-btn" data-smiley="🤔">🤔</button>
                    <button class="smiley-btn" data-smiley="😐">😐</button>
                    <button class="smiley-btn" data-smiley="😑">😑</button>
                    <button class="smiley-btn" data-smiley="😶">😶</button>
                    <button class="smiley-btn" data-smiley="🙄">🙄</button>
                    <button class="smiley-btn" data-smiley="😏">😏</button>
                    <button class="smiley-btn" data-smiley="😣">😣</button>
                    <button class="smiley-btn" data-smiley="😥">😥</button>
                    <button class="smiley-btn" data-smiley="😮">😮</button>
                    <button class="smiley-btn" data-smiley="🤐">🤐</button>
                    <button class="smiley-btn" data-smiley="😌">😌</button>
                    <button class="smiley-btn" data-smiley="😔">😔</button>
                    <button class="smiley-btn" data-smiley="😪">😪</button>
                    <button class="smiley-btn" data-smiley="🤤">🤤</button>
                    <button class="smiley-btn" data-smiley="😴">😴</button>
                    <button class="smiley-btn" data-smiley="😷">😷</button>
                    <button class="smiley-btn" data-smiley="🤒">🤒</button>
                    <button class="smiley-btn" data-smiley="🤕">🤕</button>
                    <button class="smiley-btn" data-smiley="🤢">🤢</button>
                    <button class="smiley-btn" data-smiley="🤮">🤮</button>
                    <button class="smiley-btn" data-smiley="🤧">🤧</button>
                    <button class="smiley-btn" data-smiley="🥵">🥵</button>
                    <button class="smiley-btn" data-smiley="🥶">🥶</button>
                    <button class="smiley-btn" data-smiley="🥴">🥴</button>
                    <button class="smiley-btn" data-smiley="😵">😵</button>
                    <button class="smiley-btn" data-smiley="🤯">🤯</button>
                    <button class="smiley-btn" data-smiley="🤠">🤠</button>
                    <button class="smiley-btn" data-smiley="🥳">🥳</button>
                    <button class="smiley-btn" data-smiley="😈">😈</button>
                    <button class="smiley-btn" data-smiley="👿">👿</button>
                    <button class="smiley-btn" data-smiley="💀">💀</button>
                    <button class="smiley-btn" data-smiley="👋">👋</button>
                    <button class="smiley-btn" data-smiley="🤚">🤚</button>
                    <button class="smiley-btn" data-smiley="🖐️">🖐️</button>
                    <button class="smiley-btn" data-smiley="✋">✋</button>
                    <button class="smiley-btn" data-smiley="🖖">🖖</button>
                    <button class="smiley-btn" data-smiley="👌">👌</button>
                    <button class="smiley-btn" data-smiley="🤌">🤌</button>
                    <button class="smiley-btn" data-smiley="🤏">🤏</button>
                    <button class="smiley-btn" data-smiley="✌️">✌️</button>
                    <button class="smiley-btn" data-smiley="🤞">🤞</button>
                    <button class="smiley-btn" data-smiley="🤟">🤟</button>
                    <button class="smiley-btn" data-smiley="🤘">🤘</button>
                </div>
                <div class="input-wrapper">
                    <button class="smiley-toggle" id="smiley-toggle">😊</button>
                    <textarea id="chat-input" class="chat-input" placeholder="Введите сообщение..."></textarea>
                    <button class="chat-send" id="chat-send">📤 Отправить</button>
                </div>
            </div>
        </div>
        
        <div class="chat-users-panel" id="chat-users-panel">
            <div class="users-header">👥 Онлайн (<span id="users-count">0</span>)</div>
            <div class="users-list" id="users-list"></div>
        </div>
    </div>
</div>

<style>
.chat-widget {
    position: fixed;
    bottom: 0;
    right: 0;
    left: 0;
    background: linear-gradient(135deg, #2b1d12 0%, #1f150c 100%);
    border-top: 2px solid #7b5a2f;
    border-radius: 12px 12px 0 0;
    z-index: 1000;
    transition: height 0.3s ease;
    box-shadow: 0 -5px 20px rgba(0,0,0,0.5);
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: linear-gradient(135deg, #4a2e18 0%, #3a2210 100%);
    border-bottom: 1px solid #7b5a2f;
    border-radius: 12px 12px 0 0;
    cursor: pointer;
}

.chat-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #ffd700;
    font-weight: bold;
}

.chat-online-count {
    background: #2d6a2d;
    color: #66ff66;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
}

.chat-controls button {
    background: none;
    border: none;
    color: #ffd700;
    font-size: 16px;
    cursor: pointer;
    padding: 5px 10px;
}

.chat-body {
    display: flex;
    height: calc(100% - 40px);
    min-height: 0;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.chat-users-panel {
    width: 220px;
    background: #2c1810;
    border-left: 1px solid #5c3a2a;
    display: flex;
    flex-direction: column;
}

.users-header {
    padding: 10px;
    background: #3d2317;
    color: #ffd700;
    font-weight: bold;
    border-bottom: 1px solid #5c3a2a;
}

.users-list {
    flex: 1;
    overflow-y: auto;
    padding: 5px;
}

.user-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px;
    margin: 2px 0;
    background: #3d2317;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.user-item:hover {
    background: #5c3a2a;
    transform: translateX(3px);
}

.user-name {
    color: #ffd700;
    text-decoration: none;
    font-size: 13px;
}

.user-name:hover {
    text-decoration: underline;
}

.user-level {
    color: #66ff66;
    font-size: 11px;
}

.private-btn {
    background: #6b2d2d;
    color: #ff6666;
    border: none;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 5px;
}

.private-btn:hover {
    background: #8b3d3d;
    color: #ff8888;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    background: #1f150c;
}

.message-item {
    padding: 6px 10px;
    margin: 4px 0;
    border-radius: 8px;
    font-size: 13px;
    word-wrap: break-word;
}

.message-public {
    color: #e6c8a0;
    background: rgba(0,0,0,0.3);
}

.message-private {
    color: #ff6666;
    background: rgba(107,45,45,0.3);
    border-left: 3px solid #ff6666;
}

.message-time {
    color: #8b7355;
    font-size: 10px;
    margin-right: 8px;
}

.message-from {
    color: #ffd700;
    font-weight: bold;
    margin-right: 8px;
}

.message-to {
    color: #ffaa66;
    font-size: 11px;
    margin-right: 8px;
}

.message-text {
    color: inherit;
}

.chat-input-area {
    padding: 10px;
    background: #2c1810;
    border-top: 1px solid #5c3a2a;
}

.smileys-panel {
    display: none;
    flex-wrap: wrap;
    gap: 5px;
    padding: 8px;
    background: #3d2317;
    border-radius: 8px;
    margin-bottom: 8px;
    max-height: 100px;
    overflow-y: auto;
}

.smileys-panel.show {
    display: flex;
}

.smiley-btn {
    background: #5c3a2a;
    border: none;
    color: #ffd700;
    font-size: 18px;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.smiley-btn:hover {
    background: #8b5e3c;
    transform: scale(1.1);
}

.input-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.smiley-toggle {
    background: #5c3a2a;
    border: none;
    color: #ffd700;
    font-size: 20px;
    width: 40px;
    height: 40px;
    border-radius: 6px;
    cursor: pointer;
    flex-shrink: 0;
}

.smiley-toggle:hover {
    background: #8b5e3c;
}

.chat-input {
    flex: 1;
    padding: 10px;
    background: #1f150c;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
    resize: vertical;
    font-family: inherit;
    font-size: 13px;
    min-height: 40px;
    max-height: 100px;
}

.chat-input:focus {
    outline: none;
    border-color: #ffd700;
}

.chat-send {
    background: #8b5e3c;
    color: #ffd700;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    flex-shrink: 0;
    height: 40px;
}

.chat-send:hover {
    background: #a0744f;
}

@media (max-width: 768px) {
    .chat-users-panel {
        width: 160px;
    }
    
    .user-name {
        font-size: 11px;
    }
    
    .smiley-btn {
        width: 28px;
        height: 28px;
        font-size: 14px;
    }
    
    .private-btn {
        font-size: 8px;
        padding: 1px 4px;
    }
}
.clan-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #ffd700;
    background: #2c1810;
}
</style>

<script>
var chatLastId = 0;
var chatCurrentUser = <?= Yii::$app->user->id ?>;
var chatCurrentReply = null;

function formatTime(timestamp) {
    var date = new Date(timestamp * 1000);
    return date.toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit'});
}

function loadMessages() {
    fetch('/chat/get-messages?last_id=' + chatLastId)
        .then(response => response.json())
        .then(messages => {
            if (messages.length > 0) {
                var container = document.getElementById('chat-messages');
                var scrollBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 50;
                
                for (var i = 0; i < messages.length; i++) {
                    var msg = messages[i];
                    chatLastId = msg.id;
                    
                    var div = document.createElement('div');
                    div.className = 'message-item ' + (msg.isPrivate ? 'message-private' : 'message-public');
                    
                    var timeSpan = '<span class="message-time">[' + formatTime(msg.time) + ']</span>';
                    var fromSpan = '<span class="message-from">' + escapeHtml(msg.from_name) + '</span>';
                    var textSpan = '<span class="message-text">' + escapeHtml(msg.message) + '</span>';
                    
                    var html = timeSpan + ' ' + fromSpan;
                    
                    if (msg.isPrivate && msg.to_name) {
                        html += '<span class="message-to">→ ' + escapeHtml(msg.to_name) + '</span>';
                    }
                    
                    html += ': ' + textSpan;
                    div.innerHTML = html;
                    container.appendChild(div);
                }
                
                if (scrollBottom) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        })
        .catch(error => console.error('Error loading messages:', error));
}

function loadUsers() {
    fetch('/chat/get-users')
        .then(response => response.json())
        .then(users => {
            var container = document.getElementById('users-list');
            var countSpan = document.getElementById('users-count');
            var onlineCount = document.getElementById('onlineCount');
            
            countSpan.textContent = users.length;
            if (onlineCount) onlineCount.textContent = users.length;
            
            var html = '';
            for (var i = 0; i < users.length; i++) {
                var user = users[i];
                var clanIconHtml = '';
                if (user.clan_img) {
                    clanIconHtml = '<img src="' + user.clan_img + '" class="clan-icon" alt="clan" onerror="this.style.display=\'none\'">';
                }
                html += '<div class="user-item">';
                html += '<div style="display: flex; align-items: center; gap: 6px;">';
                html += clanIconHtml;
                html += '<a href="/site/info?username=' + encodeURIComponent(user.username) + '" target="_blank" class="user-name">' + escapeHtml(user.username) + '</a>';
                html += '</div>';
                html += '<div style="display: flex; align-items: center; gap: 5px;">';
                html += '<span class="user-level">[' + user.level + ']</span>';
                if (user.id !== chatCurrentUser) {
                    html += '<button class="private-btn" data-user-id="' + user.id + '" data-username="' + escapeHtml(user.username) + '">Приват</button>';
                }
                html += '</div>';
                html += '</div>';
            }
            container.innerHTML = html;
            
            // Привязываем обработчики для кнопок приватных сообщений
            document.querySelectorAll('.private-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    var userId = this.getAttribute('data-user-id');
                    var username = this.getAttribute('data-username');
                    setPrivateReply(userId, username);
                });
            });
        })
        .catch(error => console.error('Error loading users:', error));
}

function setPrivateReply(userId, username) {
    var input = document.getElementById('chat-input');
    chatCurrentReply = userId;
    input.value = '@' + username + ' ';
    input.focus();
}

function sendMessage() {
    var input = document.getElementById('chat-input');
    var message = input.value.trim();
    
    if (message === '') return;
    
    var data = 'message=' + encodeURIComponent(message);
    if (chatCurrentReply) {
        data += '&to_user=' + chatCurrentReply;
    }
    
    fetch('/chat/send-message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
        },
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            chatCurrentReply = null;
            loadMessages();
        } else {
            alert('Ошибка: ' + data.error);
        }
    })
    .catch(error => console.error('Error sending message:', error));
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function saveChatState(height, collapsed) {
    fetch('/chat/save-state', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
        },
        body: 'height=' + height + '&collapsed=' + collapsed
    }).catch(error => console.error('Error saving chat state:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    loadUsers();
    
    setInterval(loadMessages, 10000);
    setInterval(loadUsers, 15000);
    
    var sendBtn = document.getElementById('chat-send');
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }
    
    var chatInput = document.getElementById('chat-input');
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
    
    // Кнопка сворачивания/разворачивания
    var minimizeBtn = document.getElementById('chat-minimize');
    var chatWidget = document.getElementById('chat-widget');
    var chatBody = document.getElementById('chat-body');
    if (minimizeBtn && chatWidget && chatBody) {
        var currentCollapsed = chatWidget.getAttribute('data-collapsed') === '1';
        
        minimizeBtn.addEventListener('click', function() {
            currentCollapsed = !currentCollapsed;
            var newHeight = currentCollapsed ? 40 : <?= $height ?>;
            chatWidget.style.height = newHeight + 'px';
            chatBody.style.display = currentCollapsed ? 'none' : 'flex';
            minimizeBtn.textContent = currentCollapsed ? '▲' : '▼';
            saveChatState(newHeight, currentCollapsed ? 1 : 0);
        });
    } else {
        console.error('Minimize button or chat elements not found');
    }
    
    var smileyToggle = document.getElementById('smiley-toggle');
    var smileysPanel = document.getElementById('smileys-panel');
    if (smileyToggle && smileysPanel) {
        smileyToggle.addEventListener('click', function() {
            smileysPanel.classList.toggle('show');
        });
    }
    
    var smileyBtns = document.querySelectorAll('.smiley-btn');
    for (var i = 0; i < smileyBtns.length; i++) {
        smileyBtns[i].addEventListener('click', function() {
            var smiley = this.getAttribute('data-smiley');
            if (chatInput) chatInput.value += smiley;
            if (chatInput) chatInput.focus();
        });
    }
    
    var resizeHandle = document.createElement('div');
    resizeHandle.style.cssText = 'height: 5px; cursor: ns-resize; background: #5c3a2a;';
    chatWidget.insertBefore(resizeHandle, chatWidget.firstChild);
    
    var startY, startHeight;
    resizeHandle.addEventListener('mousedown', function(e) {
        startY = e.clientY;
        startHeight = parseInt(chatWidget.style.height, 10) || <?= $height ?>;
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        e.preventDefault();
    });
    
    function onMouseMove(e) {
        var newHeight = startHeight + (startY - e.clientY);
        newHeight = Math.min(Math.max(newHeight, 150), 500);
        chatWidget.style.height = newHeight + 'px';
    }
    
    function onMouseUp() {
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
        saveChatState(parseInt(chatWidget.style.height, 10), currentCollapsed ? 1 : 0);
    }
});
</script>