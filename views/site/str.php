<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Навигация по карте</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #2c1810;
            margin: 0;
            padding: 20px;
            /* Отступ сверху для фиксированного меню */
            padding-top: 80px;
        }
        
        .image-container {
            position: relative;
            display: inline-block;
            max-width: 100%;
            /* Дополнительный отступ если нужно */
            margin-top: 20px;
        }

        .scene-image {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 12px;
            border: 3px solid #d2a45b;
        }

        .click-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
        }
        
        .click-area {
            position: absolute;
            cursor: pointer;
            background: transparent;
            transition: all 0.2s ease;
        }
        
        .click-area:hover {
            background: rgba(255, 215, 0, 0.2);
            outline: 2px solid #ffd700;
        }

        .custom-tooltip {
            position: fixed;
            background: rgba(0, 0, 0, 0.9);
            color: #ffd700;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1000;
            font-family: 'Segoe UI', sans-serif;
            border-left: 3px solid #ffd700;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .coord-panel {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0,0,0,0.7);
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            font-family: monospace;
            z-index: 1001;
        }
        
        /* Адаптация для мобильных устройств */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="/img/str.png" alt="Страшилкина улица" class="scene-image" id="mainImage">
        <div class="click-layer" id="clickLayer"></div>
    </div>

    <script>
        const img = document.getElementById('mainImage');
        const clickLayer = document.getElementById('clickLayer');
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        document.body.appendChild(tooltip);
        
        // Области с координатами (в оригинальных пикселях картинки)
        const areas = [
            { name: 'Почта', x1: 409, y1: 366, x2: 514, y2: 437, href: '/mail/index', tooltip: '📬 Почта' },
            { name: 'Аукцион', x1: 383, y1: 95, x2: 471, y2: 158, href: '/auction/index', tooltip: '💰 Аукцион' },
            { name: 'Кланы', x1: 198, y1: 303, x2: 375, y2: 420, href: '/clan/index', tooltip: '🏰 Управление кланами' },
            { name: 'ЦП', x1: 747, y1: 548, x2: 932, y2: 645, href: '/site/cp', tooltip: '🏛️ Центральная площадь' },
            { name: 'Магазин', x1: 550, y1: 200, x2: 650, y2: 280, href: '/shop/repa', tooltip: '🛒 Магазин' },
            
        ];
        
        let currentHoverArea = null;
        
        function getScale() {
            const rect = img.getBoundingClientRect();
            return {
                x: rect.width / img.naturalWidth,
                y: rect.height / img.naturalHeight
            };
        }
        
        function createClickAreas() {
            clickLayer.innerHTML = '';
            const scale = getScale();
            
            areas.forEach(area => {
                const div = document.createElement('div');
                div.className = 'click-area';
                div.style.left = (area.x1 * scale.x) + 'px';
                div.style.top = (area.y1 * scale.y) + 'px';
                div.style.width = ((area.x2 - area.x1) * scale.x) + 'px';
                div.style.height = ((area.y2 - area.y1) * scale.y) + 'px';
                
                div.addEventListener('click', () => {
                    window.location.href = area.href;
                });
                
                div.addEventListener('mouseenter', (e) => {
                    currentHoverArea = area;
                    tooltip.textContent = area.tooltip;
                    tooltip.style.display = 'block';
                    tooltip.style.left = (e.clientX + 15) + 'px';
                    tooltip.style.top = (e.clientY - 35) + 'px';
                });
                
                div.addEventListener('mousemove', (e) => {
                    tooltip.style.left = (e.clientX + 15) + 'px';
                    tooltip.style.top = (e.clientY - 35) + 'px';
                });
                
                div.addEventListener('mouseleave', () => {
                    currentHoverArea = null;
                    tooltip.style.display = 'none';
                });
                
                clickLayer.appendChild(div);
            });
        }
        
        // При загрузке и изменении размера окна пересчитываем позиции
        window.addEventListener('load', createClickAreas);
        window.addEventListener('resize', createClickAreas);
        
        // Инструмент для получения координат (клик с Ctrl)
        let isSelecting = false;
        let startPoint = null;
        
        img.addEventListener('click', (e) => {
            if (e.ctrlKey) {
                const rect = img.getBoundingClientRect();
                const scaleX = img.naturalWidth / rect.width;
                const scaleY = img.naturalHeight / rect.height;
                const x = Math.round((e.clientX - rect.left) * scaleX);
                const y = Math.round((e.clientY - rect.top) * scaleY);
                
                if (!isSelecting) {
                    isSelecting = true;
                    startPoint = { x, y };
                    tooltip.textContent = `Выбран угол: ${x}, ${y}. Теперь выбери второй угол`;
                    tooltip.style.display = 'block';
                    tooltip.style.left = (e.clientX + 15) + 'px';
                    tooltip.style.top = (e.clientY - 35) + 'px';
                } else {
                    isSelecting = false;
                    tooltip.style.display = 'none';
                    const coords = `${startPoint.x},${startPoint.y},${x},${y}`;
                    alert(`Координаты: ${coords}\n\n<area shape="rect" coords="${coords}" href="/link" alt="Название" data-tooltip="Подсказка">`);
                    startPoint = null;
                }
                e.preventDefault();
            }
        });
    </script>
</body>
</html>