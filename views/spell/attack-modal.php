<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="modal-overlay" id="attackModalOverlay">
    <div class="attack-modal">
        <div class="modal-header">
            <h2>⚔️ Нападение</h2>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Введите имя игрока, на которого хотите напасть:</p>
            
            <div class="form-group">
                <input type="text" id="playerUsername" class="attack-input" placeholder="Имя игрока" autocomplete="off">
                <button type="button" id="checkPlayerBtn" class="btn-check">🔍 Проверить</button>
            </div>
            
            <div id="playerInfo" class="player-info" style="display: none;">
                <div class="info-header">
                    <img id="playerAvatar" class="player-avatar" src="" alt="Avatar">
                    <div class="info-stats">
                        <div class="info-name"><span id="playerName"></span></div>
                        <div class="info-level">📊 Уровень: <span id="playerLevel"></span></div>
                        <div class="info-hp">❤️ Здоровье: <span id="playerHealth"></span></div>
                        <div class="info-damage">⚔️ Урон: <span id="playerDamage"></span></div>
                        <div class="info-defence">🛡️ Защита: <span id="playerDefence"></span></div>
                    </div>
                </div>
            </div>
            
            <div id="errorMessage" class="error-message" style="display: none;"></div>
            
            <div class="form-buttons" style="display: none;" id="attackButtons">
                <button type="button" id="confirmAttackBtn" class="btn-attack">⚔️ Напасть</button>
                <button type="button" id="cancelBtn" class="btn-cancel">Отмена</button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.attack-modal {
    background: linear-gradient(135deg, #2b1d12, #1f150c);
    border: 2px solid #7b5a2f;
    border-radius: 12px;
    width: 450px;
    max-width: 90%;
    padding: 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #5c3a2a;
    background: #3d2317;
    border-radius: 12px 12px 0 0;
}

.modal-header h2 {
    color: #ffd700;
    margin: 0;
    font-size: 20px;
}

.close-modal {
    background: none;
    border: none;
    color: #c9a87b;
    font-size: 24px;
    cursor: pointer;
}

.close-modal:hover {
    color: #ffd700;
}

.modal-body {
    padding: 20px;
}

.modal-body p {
    color: #c9a87b;
    margin-bottom: 15px;
}

.form-group {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.attack-input {
    flex: 1;
    padding: 10px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 6px;
    color: #ffd700;
    font-size: 14px;
}

.attack-input:focus {
    outline: none;
    border-color: #ffd700;
}

.btn-check {
    background: #5c3a2a;
    color: #c9a87b;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-check:hover {
    background: #7a4f3a;
    color: #ffd700;
}

.player-info {
    background: rgba(0,0,0,0.5);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #5c3a2a;
}

.info-header {
    display: flex;
    gap: 15px;
    align-items: center;
}

.player-avatar {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    border: 2px solid #ffd700;
}

.info-stats {
    flex: 1;
}

.info-name {
    font-size: 18px;
    font-weight: bold;
    color: #ffd700;
    margin-bottom: 5px;
}

.info-level, .info-hp, .info-damage, .info-defence {
    font-size: 12px;
    color: #c9a87b;
    margin-top: 3px;
}

.info-level span, .info-hp span, .info-damage span, .info-defence span {
    color: #ffd700;
    font-weight: bold;
}

.error-message {
    background: rgba(255,0,0,0.2);
    border: 1px solid #ff4444;
    border-radius: 6px;
    padding: 10px;
    color: #ff8888;
    margin-bottom: 15px;
    text-align: center;
}

.form-buttons {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.btn-attack {
    flex: 1;
    background: #8b5e3c;
    color: #ffd700;
    border: none;
    padding: 10px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}

.btn-attack:hover {
    background: #a0744f;
}

.btn-cancel {
    flex: 1;
    background: #5c3a2a;
    color: #c9a87b;
    border: none;
    padding: 10px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-cancel:hover {
    background: #7a4f3a;
    color: #ffd700;
}
</style>

<script>
let currentTarget = null;

function closeModal() {
    document.getElementById('attackModalOverlay')?.remove();
}

document.querySelector('.close-modal')?.addEventListener('click', closeModal);
document.getElementById('cancelBtn')?.addEventListener('click', closeModal);
document.getElementById('attackModalOverlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.getElementById('checkPlayerBtn')?.addEventListener('click', function() {
    const username = document.getElementById('playerUsername').value.trim();
    
    if (!username) {
        showError('Введите имя игрока');
        return;
    }
    
    fetch('/spell/check-player', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'username=' + encodeURIComponent(username)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentTarget = data.player;
            showPlayerInfo(data.player);
            hideError();
            document.getElementById('attackButtons').style.display = 'flex';
        } else {
            showError(data.error);
            document.getElementById('playerInfo').style.display = 'none';
            document.getElementById('attackButtons').style.display = 'none';
            currentTarget = null;
        }
    })
    .catch(error => {
        showError('Ошибка при проверке игрока');
        console.error(error);
    });
});

function showPlayerInfo(player) {
    document.getElementById('playerName').textContent = player.username;
    document.getElementById('playerLevel').textContent = player.level;
    document.getElementById('playerHealth').textContent = player.health;
    document.getElementById('playerDamage').textContent = player.damage;
    document.getElementById('playerDefence').textContent = player.defence;
    document.getElementById('playerAvatar').src = player.avatar || '/img/default-avatar.png';
    document.getElementById('playerInfo').style.display = 'block';
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

function hideError() {
    document.getElementById('errorMessage').style.display = 'none';
}

document.getElementById('confirmAttackBtn')?.addEventListener('click', function() {
    if (!currentTarget) {
        showError('Сначала проверьте игрока');
        return;
    }
    
    const username = document.getElementById('playerUsername').value.trim();
    
    fetch('/spell/attack', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'username=' + encodeURIComponent(username)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('⚔️ ' + data.message);
            window.location.href = data.redirect;
        } else {
            showError(data.error);
        }
    })
    .catch(error => {
        showError('Ошибка при атаке');
        console.error(error);
    });
});

// Enter в поле ввода
document.getElementById('playerUsername')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('checkPlayerBtn').click();
    }
});
</script>