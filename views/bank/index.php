<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Банк';
?>

<div class="bank-container">
    <div class="bank-header">
        <h1>Банк</h1>
        <p class="subtitle">Обмен валюты и пополнение счета</p>
    </div>

    <div class="balance-panel">
        <div class="balance-card">
            <div class="balance-icon">💰</div>
            <div class="balance-info">
                <span class="balance-label">Ваш баланс KR</span>
                <span class="balance-value"><?= number_format($user->kr, 0, ',', ' ') ?></span>
            </div>
        </div>
        <div class="balance-card">
            <div class="balance-icon">💎</div>
            <div class="balance-info">
                <span class="balance-label">Ваш баланс EKR</span>
                <span class="balance-value"><?= number_format($user->ekr, 0, ',', ' ') ?></span>
            </div>
        </div>
    </div>

    <div class="convert-section">
        <div class="section-title">
            <span class="title-icon">🔄</span>
            <h2>Конвертация EKR в KR</h2>
        </div>
        <div class="convert-rate">
            <span class="rate-label">Актуальный курс:</span>
            <span class="rate-value">1 EKR = 30 KR</span>
        </div>
        
        <div class="convert-form">
            <div class="input-group">
                <label for="ekr-amount">Введите количество EKR для конвертации</label>
                <div class="input-wrapper">
                    <input type="number" id="ekr-amount" class="amount-input" placeholder="Введите сумму" min="1" step="1">
                    <span class="input-suffix">EKR</span>
                </div>
            </div>
            <div class="convert-preview">
                <span class="preview-text">Вы получите:</span>
                <span class="preview-value" id="preview-value">0</span>
                <span class="preview-suffix">KR</span>
            </div>
            <button id="convertBtn" class="btn-convert">Конвертировать</button>
        </div>
    </div>

    <div class="recharge-section">
        <div class="section-title">
            <span class="title-icon">💳</span>
            <h2>Пополнение счета</h2>
        </div>
        <div class="recharge-form">
            <div class="input-group">
                <label for="recharge-amount">Введите сумму пополнения (в разработке)</label>
                <div class="input-wrapper">
                    <input type="number" id="recharge-amount" class="amount-input" placeholder="Введите сумму" min="1" step="1" disabled>
                    <span class="input-suffix">₽</span>
                </div>
            </div>
            <button id="rechargeBtn" class="btn-recharge" disabled>Пополнить (в разработке)</button>
        </div>
        <div class="recharge-note">
            <span class="note-icon">ℹ️</span>
            <span class="note-text">Пополнение счета будет доступно в ближайшее время</span>
        </div>
    </div>
</div>

<script>
// Предпросмотр конвертации
const amountInput = document.getElementById('ekr-amount');
const previewValue = document.getElementById('preview-value');

amountInput.addEventListener('input', function() {
    let amount = parseInt(this.value);
    if (isNaN(amount) || amount < 0) {
        amount = 0;
    }
    let converted = amount * 30;
    previewValue.textContent = converted.toLocaleString('ru-RU');
});

// Конвертация
document.getElementById('convertBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    let amount = parseInt(amountInput.value);
    
    if (isNaN(amount) || amount <= 0) {
        alert('Пожалуйста, введите корректное количество EKR для конвертации');
        return;
    }
    
    let userEkr = <?= $user->ekr ?>;
    
    if (amount > userEkr) {
        alert('Недостаточно EKR! У вас: ' + userEkr.toLocaleString('ru-RU') + ' EKR');
        return;
    }
    
    let confirmMsg = 'Вы уверены, что хотите конвертировать ' + amount.toLocaleString('ru-RU') + ' EKR в KR?\n\n' +
                     'По курсу 1 EKR = 30 KR вы получите:\n' +
                     (amount * 30).toLocaleString('ru-RU') + ' KR\n\n' +
                     'После конвертации у вас будет:\n' +
                     'EKR: ' + (userEkr - amount).toLocaleString('ru-RU') + '\n' +
                     'KR: ' + (<?= $user->kr ?> + (amount * 30)).toLocaleString('ru-RU');
    
    if (confirm(confirmMsg)) {
        let secondConfirm = confirm('ПОДТВЕРЖДЕНИЕ\n\nДействие необратимо. Вы действительно хотите конвертировать?');
        
        if (secondConfirm) {
            // Отправка AJAX запроса
            fetch('/bank/convert', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'amount=' + amount
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    window.location.reload();
                } else {
                    alert('✗ Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                alert('Произошла ошибка при выполнении запроса');
                console.error('Error:', error);
            });
        }
    }
});

// Пополнение (заглушка)
document.getElementById('rechargeBtn').addEventListener('click', function(e) {
    e.preventDefault();
    alert('Функция пополнения находится в разработке. Скоро она будет доступна!');
});
</script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.bank-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #2c1810;
    min-height: 100vh;
}

.bank-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.bank-header h1 {
    color: #ffd700;
    font-size: 32px;
    margin-bottom: 10px;
    text-shadow: 2px 2px 0 #2c1810;
}

.subtitle {
    color: #c9a87b;
    font-size: 14px;
}

.balance-panel {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.balance-card {
    background: #3d2317;
    border: 1px solid #5c3a2a;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s;
}

.balance-card:hover {
    transform: translateY(-3px);
    border-color: #ffd700;
}

.balance-icon {
    font-size: 48px;
}

.balance-info {
    flex: 1;
}

.balance-label {
    display: block;
    color: #c9a87b;
    font-size: 12px;
    margin-bottom: 5px;
}

.balance-value {
    display: block;
    color: #ffd700;
    font-size: 28px;
    font-weight: bold;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #5c3a2a;
}

.title-icon {
    font-size: 24px;
}

.section-title h2 {
    color: #ffd700;
    font-size: 20px;
    margin: 0;
}

.convert-section, .recharge-section {
    background: #3d2317;
    border: 1px solid #5c3a2a;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.convert-rate {
    background: #2c1810;
    border-radius: 8px;
    padding: 12px 15px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rate-label {
    color: #c9a87b;
    font-size: 14px;
}

.rate-value {
    color: #ffd700;
    font-size: 18px;
    font-weight: bold;
}

.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    color: #c9a87b;
    font-size: 13px;
    margin-bottom: 8px;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.amount-input {
    width: 100%;
    padding: 12px 60px 12px 15px;
    background: #2c1810;
    border: 1px solid #5c3a2a;
    border-radius: 8px;
    color: #ffd700;
    font-size: 16px;
    transition: all 0.2s;
}

.amount-input:focus {
    outline: none;
    border-color: #ffd700;
}

.amount-input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.input-suffix {
    position: absolute;
    right: 15px;
    color: #c9a87b;
    font-size: 14px;
}

.convert-preview {
    background: #2c1810;
    border-radius: 8px;
    padding: 12px 15px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-text {
    color: #c9a87b;
    font-size: 14px;
}

.preview-value {
    color: #66ff66;
    font-size: 20px;
    font-weight: bold;
}

.preview-suffix {
    color: #c9a87b;
    font-size: 14px;
    margin-left: 5px;
}

.btn-convert, .btn-recharge {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-convert {
    background: #8b5e3c;
    color: #ffd700;
}

.btn-convert:hover {
    background: #a0744f;
    transform: scale(1.02);
}

.btn-recharge {
    background: #3d5c3a;
    color: #88ff88;
}

.btn-recharge:hover:not(:disabled) {
    background: #5c8b5a;
    transform: scale(1.02);
}

.btn-recharge:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.recharge-note {
    margin-top: 15px;
    padding: 10px;
    background: #2c1810;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.note-icon {
    font-size: 16px;
}

.note-text {
    color: #c9a87b;
    font-size: 12px;
}

@media (max-width: 768px) {
    .bank-container {
        padding: 10px;
    }
    
    .balance-panel {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .balance-value {
        font-size: 22px;
    }
    
    .convert-preview {
        flex-direction: column;
        gap: 8px;
        text-align: center;
    }
}
</style>