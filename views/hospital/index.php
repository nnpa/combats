<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Госпиталь';
?>

<div class="hospital-container">
    <div class="hospital-header">
        <h1>Госпиталь</h1>
        <p class="subtitle">Здесь вы можете сбросить все характеристики и снять всю экипировку</p>
    </div>

    <div class="info-panel">
        <div class="warning-box">
            <div class="warning-icon">⚠️</div>
            <div class="warning-text">
                <strong>Внимание!</strong> При сбросе характеристик:
                <ul>
                    <li>Вся экипировка будет снята и отправлена в инвентарь</li>
                    <li>Базовые характеристики будут сброшены до 3</li>
                    <li>Очки характеристик будут пересчитаны согласно вашему уровню</li>
                    <li>Все бонусы от предметов будут утеряны до повторной экипировки</li>
                </ul>
            </div>
        </div>
        
        <div class="current-stats">
            <h3>Текущие характеристики</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-label">Сила:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->str ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Ловкость:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->dex ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Выносливость:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->endu ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Интеллект:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->inte ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Интуиция:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->intu ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Огонь:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->fire ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Вода:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->water ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Воздух:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->air ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Земля:</span>
                    <span class="stat-value"><?= Yii::$app->user->identity->earth ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Очки статов:</span>
                    <span class="stat-value points"><?= Yii::$app->user->identity->points ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="reset-form">
        <button id="resetButton" class="btn-reset">
            Сбросить характеристики
        </button>
        <p class="reset-note">После сброса вы сможете перераспределить очки характеристик заново</p>
    </div>
</div>

<script>
document.getElementById('resetButton').addEventListener('click', function(e) {
    e.preventDefault();
    
    let confirmed = confirm(
        'ВНИМАНИЕ! Вы собираетесь сбросить все характеристики.\n\n' +
        'Это действие:\n' +
        '- Снимет всю экипировку\n' +
        '- Сбросит базовые характеристики до 3\n' +
        '- Пересчитает очки характеристик\n\n' +
        'Вы уверены, что хотите продолжить?'
    );
    
    if (confirmed) {
        let secondConfirm = confirm(
            'ПОДТВЕРДИТЕ СБРОС\n\n' +
            'Это действие нельзя отменить. Вы действительно хотите сбросить все характеристики?'
        );
        
        if (secondConfirm) {
            // Отправляем AJAX запрос
            fetch('/hospital/reset-stats', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message + '\n\nСтраница будет перезагружена для обновления данных.');
                    window.location.reload();
                } else {
                    alert('✗ Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                alert('Произошла ошибка при выполнении запроса. Пожалуйста, попробуйте позже.');
                console.error('Error:', error);
            });
        }
    }
});
</script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.hospital-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #2c1810;
    min-height: 100vh;
}

.hospital-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.hospital-header h1 {
    color: #ffd700;
    font-size: 32px;
    margin-bottom: 10px;
    text-shadow: 2px 2px 0 #2c1810;
}

.subtitle {
    color: #c9a87b;
    font-size: 14px;
}

.info-panel {
    margin-bottom: 30px;
}

.warning-box {
    background: #3d2a1a;
    border: 2px solid #ff6600;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    display: flex;
    gap: 15px;
}

.warning-icon {
    font-size: 40px;
}

.warning-text {
    flex: 1;
    color: #ffaa66;
}

.warning-text strong {
    color: #ff6600;
    font-size: 16px;
    display: block;
    margin-bottom: 10px;
}

.warning-text ul {
    margin-top: 10px;
    padding-left: 20px;
}

.warning-text li {
    margin: 5px 0;
    font-size: 13px;
}

.current-stats {
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #5c3a2a;
}

.current-stats h3 {
    color: #ffd700;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #5c3a2a;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}

.stat-item {
    background: #2c1810;
    padding: 10px 15px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-label {
    color: #c9a87b;
    font-size: 14px;
}

.stat-value {
    color: #ffd700;
    font-size: 18px;
    font-weight: bold;
}

.stat-value.points {
    color: #66ff66;
}

.reset-form {
    text-align: center;
    margin-top: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.btn-reset {
    background: #8b3c3c;
    color: #ffaa66;
    border: none;
    padding: 15px 40px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-reset:hover {
    background: #b54c4c;
    color: #ffd700;
    transform: scale(1.02);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.reset-note {
    margin-top: 15px;
    color: #c9a87b;
    font-size: 12px;
}

@media (max-width: 768px) {
    .hospital-container {
        padding: 10px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .warning-box {
        flex-direction: column;
        text-align: center;
    }
    
    .warning-icon {
        font-size: 48px;
    }
    
    .btn-reset {
        width: 100%;
        padding: 12px 20px;
        font-size: 16px;
    }
}
</style>