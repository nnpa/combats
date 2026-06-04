<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Навигация по карте</title>
    <style>
        .image-container {
            position: relative;
            display: inline-block;
        }

        .scene-image {
            width: 100%;
            max-width: 1200px;
            display: block;
        }

        /* Этот блок позволяет подсвечивать area через обёртку и canvas-подход */
        .image-container {
            position: relative;
            display: inline-block;
        }

        /* Слой для подсветки поверх карты */
        .highlight-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        /* Подсвечиваемая область (динамически создаётся) */
        .highlight-rect {
            position: absolute;
            outline: 3px solid gold;
            outline-offset: 0;
            background: rgba(255, 215, 0, 0.15);
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 10;
        }

        /* Кастомная всплывающая подсказка */
        .custom-tooltip {
            position: fixed;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1000;
            font-family: sans-serif;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="/img/cp.jpeg" alt="Площадь с замком" usemap="#map1" class="scene-image">
        <map name="map1">
            <!-- Арена -->
            <area shape="rect" coords="453,357,780,465" href="/battle/index" alt="Арена" data-tooltip="Арена">
            <!-- Пещера -->
            <area shape="rect" coords="509,124,720,281" href="/dungeon/start" alt="Пещера" data-tooltip="Пещера">
            <!-- Магазин -->
            <area shape="rect" coords="147,200,418,332" href="/shop/index" alt="Магазин" data-tooltip="Магазин">
            <!-- Банк (исправлены кавычки) -->
            <area shape="rect" coords="489,530,771,715" href="/bank/" alt="Банк" data-tooltip="Банк">
            <!-- Кузница -->
            <area shape="rect" coords="116,482,412,598" href="/forge/index" alt="Кузница" data-tooltip="Кузница">
            <!-- Больница -->
            <area shape="rect" coords="845,467,1146,655" href="/hospital/index" alt="Больница" data-tooltip="Больница">
            <!-- Табличка влево -->
            <area shape="rect" coords="140,374,364,416" href="left.html" alt="Табличка влево" data-tooltip="ВЛЕВО">
            <!-- Табличка вправо -->
            <area shape="rect" coords="909,369,1129,422" href="right.html" alt="Табличка вправо" data-tooltip="ВПРАВО">
            
            <area shape="rect" coords="815,182,1078,330" href="/shop/art" alt="Березка" data-tooltip="Березка">

        </map>

        <!-- Слой для подсветки -->
        <div class="highlight-layer"></div>
    </div>

    <script>
        // Создаём элемент для подсказки
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        document.body.appendChild(tooltip);

        // Получаем изображение и карту
        const img = document.querySelector('.scene-image');
        const map = document.querySelector('map');
        const areas = document.querySelectorAll('area');
        const highlightLayer = document.querySelector('.highlight-layer');

        // Функция для получения масштаба (так как картинка может быть уменьшена CSS)
        function getScale() {
            const rect = img.getBoundingClientRect();
            const naturalWidth = img.naturalWidth;
            const naturalHeight = img.naturalHeight;
            return {
                x: rect.width / naturalWidth,
                y: rect.height / naturalHeight
            };
        }

        // Функция для подсветки области
        let currentHighlight = null;

        function highlightArea(area) {
            // Удаляем предыдущую подсветку
            if (currentHighlight) {
                currentHighlight.remove();
                currentHighlight = null;
            }

            if (!area) return;

            // Получаем координаты из coords
            const coords = area.getAttribute('coords');
            if (!coords) return;

            const [x1, y1, x2, y2] = coords.split(',').map(Number);
            const scale = getScale();

            // Создаём div для подсветки
            const highlightDiv = document.createElement('div');
            highlightDiv.className = 'highlight-rect';
            highlightDiv.style.left = (x1 * scale.x) + 'px';
            highlightDiv.style.top = (y1 * scale.y) + 'px';
            highlightDiv.style.width = ((x2 - x1) * scale.x) + 'px';
            highlightDiv.style.height = ((y2 - y1) * scale.y) + 'px';

            highlightLayer.appendChild(highlightDiv);
            currentHighlight = highlightDiv;
        }

        // Показываем подсказку
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

        // Добавляем обработчики для каждой области
        areas.forEach(area => {
            area.addEventListener('mouseenter', (e) => {
                highlightArea(area);
                showTooltip(area, e);
            });

            area.addEventListener('mousemove', (e) => {
                moveTooltip(e);
            });

            area.addEventListener('mouseleave', () => {
                highlightArea(null);
                hideTooltip();
            });
        });

        // Обновляем подсветку при изменении размера окна (ресайз)
        window.addEventListener('resize', () => {
            if (currentHighlight) {
                // Находим активную область заново (сохраним ссылку)
                const activeArea = Array.from(areas).find(area => 
                    area.getAttribute('data-tooltip') === currentHighlight?.parentElement?.querySelector('.highlight-rect')?.getAttribute('data-tooltip')
                );
                if (activeArea) highlightArea(activeArea);
            }
        });

        // Небольшой фикс: сохраняем последнюю подсвеченную область
        let lastHighlightedArea = null;
        const originalHighlightArea = highlightArea;
        window.highlightArea = function(area) {
            lastHighlightedArea = area;
            originalHighlightArea(area);
        };
    </script>
</body>
</html>