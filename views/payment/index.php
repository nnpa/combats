<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = 'Пополнение баланса';
?>

<div class="payment-container">
    <div class="payment-card">
        <h2>💰 30 EKR = 900 ₽</h2>
        <p>Пополните баланс, чтобы покупать предметы в игре.</p>
        <button id="buyButton" class="btn-buy">Купить за 900 ₽</button>
        <div id="payment-form"></div>
    </div>
</div>

<script src="https://yookassa.ru/checkout-widget/v1/checkout-widget.js"></script>
<script>
    const buyBtn = document.getElementById('buyButton');
    buyBtn.addEventListener('click', function() {
        buyBtn.disabled = true;
        buyBtn.textContent = 'Обработка...';

        fetch('<?= Url::to(['/payment/create']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const checkout = new window.YooMoneyCheckoutWidget({
                    confirmation_token: data.confirmation_token,
                    return_url: '<?= Url::to(['/payment/success'], true) ?>',
                    error_callback: function(error) {
                        console.error(error);
                        alert('Ошибка при загрузке формы оплаты');
                        buyBtn.disabled = false;
                        buyBtn.textContent = 'Купить за 900 ₽';
                    }
                });
                checkout.render('payment-form');
            } else {
                alert(data.error || 'Ошибка создания платежа');
                buyBtn.disabled = false;
                buyBtn.textContent = 'Купить за 900 ₽';
            }
        })
        .catch(error => {
            console.error(error);
            alert('Сетевая ошибка');
            buyBtn.disabled = false;
            buyBtn.textContent = 'Купить за 900 ₽';
        });
    });
</script>

<style>
.payment-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    background: linear-gradient(135deg, #1a0f0a, #0d0805);
}
.payment-card {
    background: #2b1d12;
    border: 2px solid #7b5a2f;
    border-radius: 24px;
    padding: 30px;
    text-align: center;
    width: 100%;
    max-width: 500px;
    color: #ffd700;
    box-shadow: 0 8px 20px rgba(0,0,0,0.5);
}
.payment-card h2 {
    font-size: 28px;
    margin-bottom: 10px;
}
.btn-buy {
    background: linear-gradient(135deg, #8b5e3c, #5a3d1f);
    border: 1px solid #d2a45b;
    border-radius: 12px;
    padding: 12px 24px;
    font-size: 18px;
    font-weight: bold;
    color: #ffd700;
    cursor: pointer;
    transition: all 0.3s;
}
.btn-buy:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #a0744f, #7a4f2f);
}
.btn-buy:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
#payment-form {
    margin-top: 20px;
}
</style>