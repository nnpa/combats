<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Центральная площадь';

// Проверяем режим разработки
$isDev = YII_ENV_DEV;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Центральная площадь</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Cinzel', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            padding-top: 20px;
        }
        
        .main-container {
            position: relative;
            display: inline-block;
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        .scene-image {
            width: 100%;
            max-width: 1200px;
            display: block;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: 3px solid #d4af37;
        }

        .highlight-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .highlight-rect {
            position: absolute;
            outline: 3px solid gold;
            outline-offset: 0;
            background: rgba(255, 215, 0, 0.15);
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 10;
            border-radius: 5px;
        }

        .custom-tooltip {
            position: fixed;
            background: rgba(0, 0, 0, 0.9);
            color: #d4af37;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1000;
            font-family: 'Cinzel', monospace;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            border-left: 3px solid #d4af37;
        }
        
        .event-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .event-btn {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            border: none;
            padding: 15px 25px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(231,76,60,0.4);
            font-family: 'Cinzel', serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .event-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(231,76,60,0.6);
        }
        
        .event-btn:disabled {
            background: #555;
            transform: none;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .event-info {
            background: rgba(0,0,0,0.85);
            border-radius: 15px;
            padding: 15px;
            margin-top: 10px;
            font-size: 12px;
            color: #ccc;
            text-align: center;
            border: 1px solid #d4af37;
        }
        
        .event-timer {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            font-family: monospace;
            margin: 5px 0;
        }
        
        .event-boss-name {
            color: #d4af37;
            font-weight: bold;
        }
        
        .no-event {
            background: rgba(0,0,0,0.7);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            color: #888;
        }
        
        .warning-message {
            color: #f39c12;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .success-message {
            color: #2ecc71;
            font-size: 12px;
            margin-top: 10px;
        }
        
        .hp-bar-boss {
            background: #4a4a4a;
            border-radius: 10px;
            height: 12px;
            margin: 8px 0;
            overflow: hidden;
        }
        
        .hp-fill-boss {
            background: linear-gradient(90deg, #e74c3c, #c0392b);
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 10px;
        }
        
        /* Стили для тестовых кнопок */
        .test-panel {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0,0,0,0.8);
            border-radius: 10px;
            padding: 10px;
            z-index: 200;
            border: 1px solid #d4af37;
            font-size: 12px;
        }
        
        .test-panel h4 {
            color: #d4af37;
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        
        .test-btn {
            background: #34495e;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin: 2px;
            font-size: 11px;
        }
        
        .test-btn:hover {
            background: #2c3e50;
        }
        
        @media (max-width: 768px) {
            .event-btn {
                padding: 10px 15px;
                font-size: 14px;
            }
            .event-timer {
                font-size: 18px;
            }
            .test-panel {
                font-size: 10px;
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="image-container">
            <img src="/img/cp.jpeg" alt="Площадь с замком" usemap="#map1" class="scene-image">
            <map name="map1">
                <area shape="rect" coords="453,357,780,465" href="/battle/index" alt="Арена" data-tooltip="🏟️ Арена">
                <area shape="rect" coords="509,124,720,281" href="/dungeon/start" alt="Пещера" data-tooltip="🕳️ Пещера">
                <area shape="rect" coords="147,200,418,332" href="/shop/index" alt="Магазин" data-tooltip="🛒 Магазин">
                <area shape="rect" coords="489,530,771,715" href="/bank/index" alt="Банк" data-tooltip="🏦 Банк">
                <area shape="rect" coords="116,482,412,598" href="/forge/index" alt="Кузница" data-tooltip="⚒️ Кузница">
                <area shape="rect" coords="845,467,1146,655" href="/hospital/index" alt="Больница" data-tooltip="🏥 Больница">
                <area shape="rect" coords="140,374,364,416" href="/site/str" alt="Табличка влево" data-tooltip="⬅️ Страшилкина улица">
                <area shape="rect" coords="909,369,1129,422" href="right.html" alt="Табличка вправо" data-tooltip="➡️ ВПРАВО">
                <area shape="rect" coords="815,182,1078,330" href="/shop/art" alt="Березка" data-tooltip="🎨 Березка">
            </map>

            <div class="highlight-layer"></div>
        </div>
        
        <!-- Плашка с информацией о враге - под картинкой -->
        <div class="event-notification" style="position: static; margin-top: 20px;">
            <div id="eventContent">Загрузка...</div>
        </div>

    </div>
    
            <?php if ($isDev): ?>
            <!-- Тестовая панель (только для разработки) -->
            <div class="test-panel">
                <h4>🔧 Тестовое управление ивентом</h4>
                <div>
                    <button class="test-btn" onclick="testStart(10)">🚀 Запустить ивент (10 мин)</button>
                    <button class="test-btn" onclick="testStart(5)">🚀 Запустить ивент (5 мин)</button>
                    <button class="test-btn" onclick="testEnd()">⏹️ Завершить ивент</button>
                </div>
                <div style="margin-top: 5px; font-size: 10px; color: #888;">
                    ⚠️ Урон боссу наносится через обычные атаки в бою
                </div>
            </div>
            <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        document.body.appendChild(tooltip);

        const img = document.querySelector('.scene-image');
        const areas = document.querySelectorAll('area');
        const highlightLayer = document.querySelector('.highlight-layer');
        
        let timerInterval = null;

        function getScale() {
            const rect = img.getBoundingClientRect();
            return {
                x: rect.width / img.naturalWidth,
                y: rect.height / img.naturalHeight
            };
        }

        let currentHighlight = null;

        function highlightArea(area) {
            if (currentHighlight) {
                currentHighlight.remove();
                currentHighlight = null;
            }
            if (!area) return;

            const coords = area.getAttribute('coords');
            if (!coords) return;

            const [x1, y1, x2, y2] = coords.split(',').map(Number);
            const scale = getScale();

            const highlightDiv = document.createElement('div');
            highlightDiv.className = 'highlight-rect';
            highlightDiv.style.left = (x1 * scale.x) + 'px';
            highlightDiv.style.top = (y1 * scale.y) + 'px';
            highlightDiv.style.width = ((x2 - x1) * scale.x) + 'px';
            highlightDiv.style.height = ((y2 - y1) * scale.y) + 'px';

            highlightLayer.appendChild(highlightDiv);
            currentHighlight = highlightDiv;
        }

        function showTooltip(area, event) {
            const text = area.getAttribute('data-tooltip');
            if (text) {
                tooltip.textContent = text;
                tooltip.style.display = 'block';
                tooltip.style.left = (event.pageX + 12) + 'px';
                tooltip.style.top = (event.pageY - 30) + 'px';
            }
        }

        function hideTooltip() {
            tooltip.style.display = 'none';
        }

        function moveTooltip(event) {
            if (tooltip.style.display === 'block') {
                tooltip.style.left = (event.pageX + 12) + 'px';
                tooltip.style.top = (event.pageY - 30) + 'px';
            }
        }

        areas.forEach(area => {
            area.addEventListener('mouseenter', (e) => {
                highlightArea(area);
                showTooltip(area, e);
            });
            area.addEventListener('mousemove', moveTooltip);
            area.addEventListener('mouseleave', () => {
                highlightArea(null);
                hideTooltip();
            });
        });

        window.addEventListener('resize', () => {
            if (currentHighlight) {
                const activeArea = Array.from(areas).find(area => 
                    area.getAttribute('data-tooltip') === currentHighlight?.getAttribute('data-tooltip')
                );
                if (activeArea) highlightArea(activeArea);
            }
        });

        // ========== ЛОГИКА ИВЕНТА ==========
        
        function updateEventTimer(endTime, elementId) {
            if (!endTime) return;
            
            const now = Math.floor(Date.now() / 1000);
            const diff = endTime - now;
            
            if (diff <= 0) {
                document.getElementById(elementId).innerHTML = '00:00:00';
                if (timerInterval) clearInterval(timerInterval);
                loadEventInfo();
                return;
            }
            
            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;
            
            document.getElementById(elementId).innerHTML = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }
        
        function updateBossHp(currentHp, maxHp) {
            const percent = (currentHp / maxHp) * 100;
            const fillEl = document.getElementById('bossHpFill');
            const textEl = document.getElementById('bossHpText');
            if (fillEl) fillEl.style.width = percent + '%';
            if (textEl) textEl.innerHTML = `❤️ ${Math.floor(currentHp)} / ${maxHp}`;
        }
        
        function loadEventInfo() {
            $.get('/event/info', function(data) {
                const container = document.getElementById('eventContent');
                
                if (data.success && data.active) {
                    const timeLeft = data.event.time_left;
                    const endTime = Math.floor(Date.now() / 1000) + timeLeft;
                    const isParticipating = <?php echo isset($isParticipant) && $isParticipant ? 'true' : 'false'; ?>;
                    const inBattle = <?php echo isset($inBattle) && $inBattle ? 'true' : 'false'; ?>;
                    
                    let buttonHtml = '';
                    
                    if (inBattle) {
                        buttonHtml = `
                            <div class="event-info">
                                <div>⚔️ Вы уже в бою с боссом!</div>
                                <a href="/battle/battle" style="color: #d4af37; text-decoration: none;">➡️ Перейти к бою</a>
                            </div>
                        `;
                    } else if (isParticipating) {
                        buttonHtml = `
                            <div class="event-info">
                                <div>🏆 Вы уже сразились с боссом в этом ивенте!</div>
                            </div>
                        `;
                    } else {
                        const hpPercent = (data.event.bot_current_hp / data.event.bot_max_hp) * 100;
                        buttonHtml = `
                            <button class="event-btn" id="defendBtn">
                                🛡️ ЗАЩИТИТЬ ГОРОД
                            </button>
                            <div class="event-info">
                                <div>👾 <span class="event-boss-name">${data.event.bot_name}</span></div>
                                <div class="hp-bar-boss">
                                    <div class="hp-fill-boss" id="bossHpFill" style="width: ${hpPercent}%"></div>
                                </div>
                                <div id="bossHpText">❤️ ${data.event.bot_current_hp} / ${data.event.bot_max_hp}</div>
                                <div>👥 Участников: ${data.event.participants} | 🏆 Побед: ${data.event.winners}</div>
                                <div>⏳ Осталось: <span id="eventTimer" class="event-timer">--:--:--</span></div>
                            </div>
                            <div id="eventMessage"></div>
                        `;
                    }
                    
                    container.innerHTML = buttonHtml;
                    
                    if (!inBattle && !isParticipating) {
                        $('#defendBtn').on('click', defendCity);
                        if (timeLeft) {
                            updateEventTimer(endTime, 'eventTimer');
                            if (timerInterval) clearInterval(timerInterval);
                            timerInterval = setInterval(() => updateEventTimer(endTime, 'eventTimer'), 1000);
                        }
                        updateBossHp(data.event.bot_current_hp, data.event.bot_max_hp);
                    }
                    
                } else {
                    container.innerHTML = `
                        <div class="no-event">
                            <div>😴 Город в безопасности</div>
                            <div style="font-size: 11px; margin-top: 5px;">Нет активных угроз</div>
                        </div>
                    `;
                }
            }).fail(function() {
                document.getElementById('eventContent').innerHTML = `
                    <div class="no-event">
                        <div>⚠️ Ошибка загрузки</div>
                    </div>
                `;
            });
        }
        
        function defendCity() {
            const btn = $('#defendBtn');
            btn.prop('disabled', true).html('⏳ Загрузка...');
            $('#eventMessage').removeClass('warning-message success-message').html('');
            
            $.post('/event/defend', function(data) {
                if (data.success) {
                    $('#eventMessage').addClass('success-message').html('✅ Бой начат! Перенаправление...');
                    setTimeout(function() {
                        window.location.href = '/battle/battle';
                    }, 1500);
                } else {
                    $('#eventMessage').addClass('warning-message').html('⚠️ ' + data.error);
                    btn.prop('disabled', false).html('🛡️ ЗАЩИТИТЬ ГОРОД');
                }
            }, 'json').fail(function() {
                $('#eventMessage').addClass('warning-message').html('⚠️ Ошибка соединения');
                btn.prop('disabled', false).html('🛡️ ЗАЩИТИТЬ ГОРОД');
            });
        }
        
        // ========== ТЕСТОВЫЕ ФУНКЦИИ (только запуск/завершение ивента) ==========
        <?php if ($isDev): ?>
        function testStart(duration) {
            if (!confirm('Запустить тестовый ивент на ' + duration + ' минут?')) return;
            
            $.get('/event/test-start', { duration: duration, bot_id: 16 }, function(data) {
                if (data.success) {
                    alert('✅ Ивент запущен! Босс появился на площади.');
                    location.reload();
                } else {
                    alert('❌ Ошибка: ' + data.error);
                }
            });
        }
        
        function testEnd() {
            if (!confirm('Завершить текущий ивент?')) return;
            
            $.get('/event/test-end', function(data) {
                if (data.success) {
                    alert('✅ Ивент завершен! Босс исчез.');
                    location.reload();
                } else {
                    alert('❌ Ошибка: ' + data.error);
                }
            });
        }
        <?php endif; ?>
        
        $(document).ready(function() {
            loadEventInfo();
            setInterval(loadEventInfo, 10000);
        });
    </script>
</body>
</html>