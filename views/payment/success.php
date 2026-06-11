<?php
$this->title = 'Оплата успешна';
?>
<div class="success-page">
    <h1>✅ Спасибо за покупку!</h1>
    <p>Ваш баланс пополнен. Теперь вы можете вернуться в игру.</p>
    <?= \yii\helpers\Html::a('Вернуться в игру', ['/site/index'], ['class' => 'btn']) ?>
</div>

<style>
.success-page {
    text-align: center;
    margin-top: 100px;
}
.btn {
    background: #28a745;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
}
</style>