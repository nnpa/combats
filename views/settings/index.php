<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Настройки персонажа';
?>

<div class="settings-container">
    <div class="settings-header">
        <h1>Настройки</h1>
        <p class="subtitle">Выбор аватара</p>
    </div>

    <div class="settings-content">
        <!-- Левая колонка - Текущий аватар -->
        <div class="current-avatar-section">
            <div class="section-title">
                <span>Текущий аватар</span>
            </div>
            <div class="current-avatar-wrapper">
                <div class="current-avatar-frame">
                    <img src="<?= $currentAvatar ?>" alt="Текущий аватар" class="current-avatar-img">
                </div>
                <div class="current-avatar-name"><?= Html::encode($user->username) ?></div>
            </div>
        </div>

        <!-- Правая колонка - Выбор нового аватара -->
        <div class="avatars-section">
            <div class="section-title">
                <span>Выберите новый аватар</span>
            </div>
            
            <?php if (empty($avatars)): ?>
                <div class="empty-message">Аватары не найдены</div>
            <?php else: ?>
                <?php $form = ActiveForm::begin(['action' => ['saveavatar'], 'method' => 'post', 'id' => 'avatar-form']); ?>
                
                <div class="avatars-grid">
                    <?php foreach ($avatars as $avatar): ?>
                        <div class="avatar-card <?= ($currentAvatar == $avatar->img) ? 'selected' : '' ?>" data-avatar-id="<?= $avatar->id ?>">
                            <div class="avatar-preview">
                                <img src="<?= $avatar->img ?>" alt="Аватар">
                            </div>
                            <div class="avatar-name"><?= Html::encode($avatar->name) ?></div>
                            <div class="avatar-check">
                                <input type="radio" name="avatar" value="<?= $avatar->id ?>" id="avatar_<?= $avatar->id ?>" <?= ($currentAvatar == $avatar->img) ? 'checked' : '' ?>>
                                <label for="avatar_<?= $avatar->id ?>" class="avatar-select-btn">Выбрать</label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-save">Сохранить аватар</button>
                </div>
                
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.settings-container {
    width: 100%;
    max-width: 100%;
    padding: 20px;
    background: #2c1810;
    min-height: 100vh;
}

.settings-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #3d2317;
    border-radius: 12px;
    border: 1px solid #5c3a2a;
}

.settings-header h1 {
    color: #ffd700;
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #c9a87b;
    font-size: 14px;
}

.settings-content {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

/* Левая колонка - текущий аватар */
.current-avatar-section {
    flex-shrink: 0;
    width: 160px;
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #5c3a2a;
}

.section-title {
    font-size: 18px;
    font-weight: bold;
    color: #ffd700;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #5c3a2a;
    text-align: center;
}

.current-avatar-wrapper {
    text-align: center;
}

.current-avatar-frame {
    width: 120px;
    height: 280px;
    margin: 0 auto 15px;
    background: #2c1810;
    border: 2px solid #ffd700;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.current-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
}

.current-avatar-name {
    color: #c9a87b;
    font-size: 14px;
    word-wrap: break-word;
}

/* Правая колонка - аватары сеткой */
.avatars-section {
    flex: 1;
    background: #3d2317;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #5c3a2a;
}

.avatars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
    max-height: 500px;
    overflow-y: auto;
    padding: 10px;
}

.avatar-card {
    background: #2c1810;
    border: 2px solid #5c3a2a;
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    transition: all 0.2s;
    cursor: pointer;
}

.avatar-card:hover {
    transform: translateY(-3px);
    border-color: #ffd700;
}

.avatar-card.selected {
    border-color: #ffd700;
    background: #4a2e1f;
}

.avatar-preview {
    width: 80px;
    height: 180px;
    margin: 0 auto 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
}

.avatar-name {
    color: #c9a87b;
    font-size: 11px;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.avatar-check {
    margin-top: 5px;
}

.avatar-select-btn {
    display: inline-block;
    background: #5c3a2a;
    color: #c9a87b;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s;
}

.avatar-card.selected .avatar-select-btn {
    background: #8b5e3c;
    color: #ffd700;
}

.avatar-select-btn:hover {
    background: #7a4f3a;
    color: #ffd700;
}

.form-buttons {
    text-align: center;
    padding-top: 15px;
    border-top: 1px solid #5c3a2a;
}

.btn-save {
    background: #8b5e3c;
    color: #ffd700;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save:hover {
    background: #a0744f;
    transform: scale(1.02);
}

.empty-message {
    text-align: center;
    padding: 40px;
    color: #c9a87b;
}

/* Адаптация под мобильные */
@media (max-width: 768px) {
    .settings-content {
        flex-direction: column;
        align-items: center;
    }
    
    .current-avatar-section {
        width: 100%;
        max-width: 200px;
    }
    
    .avatars-grid {
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    }
}

@media (max-width: 480px) {
    .avatars-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
    
    .avatar-preview {
        width: 60px;
        height: 140px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Выбор аватара при клике на карточку
    const avatarCards = document.querySelectorAll('.avatar-card');
    const form = document.getElementById('avatar-form');
    
    avatarCards.forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        
        // Если radio уже выбран при загрузке, добавляем класс selected
        if (radio && radio.checked) {
            card.classList.add('selected');
        }
        
        // Клик по карточке
        card.addEventListener('click', function(e) {
            // Если кликнули не на label и не на кнопку
            if (e.target.tagName !== 'LABEL' && 
                e.target.tagName !== 'INPUT' && 
                !e.target.classList.contains('avatar-select-btn')) {
                const radioBtn = this.querySelector('input[type="radio"]');
                if (radioBtn) {
                    radioBtn.checked = true;
                    
                    // Убираем selected у всех карточек
                    avatarCards.forEach(c => c.classList.remove('selected'));
                    // Добавляем selected текущей
                    this.classList.add('selected');
                }
            }
        });
    });
    
    // Отправка формы через submit
    const saveButton = document.querySelector('.btn-save');
    if (saveButton) {
        saveButton.addEventListener('click', function(e) {
            const selectedRadio = document.querySelector('input[name="avatar"]:checked');
            if (!selectedRadio) {
                e.preventDefault();
                alert('Пожалуйста, выберите аватар');
                return false;
            }
            form.submit();
        });
    }
});
</script>