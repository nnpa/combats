<?php
// В самом начале файла, до вывода HTML
$instanceId = $instance->id ?? null;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Псевдо-3D Лабиринт - RPG</title>
    <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
    <meta name="instance-id" content="<?= $instanceId ?>">
    <style>
        /* ... все стили остаются без изменений ... */
        * {
            user-select: none;
        }
        
        body { 
            margin: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #1a1a1a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        #game { 
            display: flex; 
            background: #1a1a1a;
        }
        
#map { 
    width: 320px; 
    background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
    border-right: 3px solid #e74c3c;
    padding: 10px;
    box-shadow: 4px 0 15px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    overflow-y: auto; /* Добавляем прокрутку если не помещается */
    max-height: 100vh;
}

#map canvas {
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
    border-radius: 8px;
    border: 2px solid #e74c3c;
    background: #ecf0f1;
    max-width: 100%;
    height: auto;
}

.stats {
    margin-top: 15px;
    color: #ecf0f1;
    font-family: monospace;
    font-size: 12px;
    background: rgba(0,0,0,0.5);
    padding: 10px;
    border-radius: 8px;
    width: 100%;
    text-align: center;
    box-sizing: border-box;
}
        
        .stat-item {
            margin: 5px 0;
            padding: 3px;
            background: rgba(0,0,0,0.3);
            border-radius: 4px;
        }
        
        .cooldown-bar {
            width: 100%;
            height: 6px;
            background: #34495e;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .cooldown-fill {
            height: 100%;
            background: #e74c3c;
            width: 100%;
            transition: width 0.05s linear;
        }
        
        #view { 
            width: 500px;
            height: 300px;
            background: #000; 
            position: relative; 
            cursor: crosshair;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        #view canvas {
            width: 100%;
            height: 100%;
            display: block;
        }
        
        .info {
            position: fixed;
            bottom: 10px;
            left: 270px;
            background: rgba(0,0,0,0.85);
            color: #ecf0f1;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-family: monospace;
            z-index: 100;
            border-left: 3px solid #e74c3c;
            pointer-events: none;
        }
        
        .message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.85);
            color: #f1c40f;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-family: monospace;
            z-index: 100;
            border-right: 3px solid #f1c40f;
            animation: slideIn 0.3s ease-out;
            max-width: 250px;
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
        
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.95);
            color: #2ecc71;
            padding: 20px;
            border-radius: 10px;
            z-index: 2000;
            font-family: monospace;
            font-size: 16px;
            text-align: center;
            border: 2px solid #2ecc71;
            box-shadow: 0 0 30px rgba(46,204,113,0.3);
        }
        
        .loading::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }
        
        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }
        
        .texture-preview {
            margin-top: 10px;
            padding: 5px;
            background: rgba(0,0,0,0.3);
            border-radius: 4px;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>
<body>
<div id="game">
    <div id="map">
        <div id="minimap"></div>
        <div class="stats">
            <div class="stat-item">📍 Позиция: <span id="posX">0</span>, <span id="posY">0</span></div>
            <div class="stat-item">🎯 Направление: <span id="direction">Восток →</span></div>
            <div class="stat-item">⚔️ Монстров: <span id="monsterCount">0</span></div>
            <div class="stat-item">⏱️ Кулдаун движения</div>
            <div class="cooldown-bar">
                <div class="cooldown-fill" id="cooldownFill"></div>
            </div>

            <div class="texture-preview" id="texturePreview">
                🎨 Загрузка текстур...
            </div>
            <div class="stat-item" style="font-size: 11px; margin-top: 10px;">
                <a href="/dungeon/exit">Выход</a>
            </div>
        </div>
    </div>
    <div id="view"></div>
</div>
<div class="info">
    🎮 Управление: ← → (поворот 90°) | ↑ (вперед) | ↓ (назад)<br>
    🖱️ Клик на монстра для атаки
</div>
<div class="loading" id="loading">Загрузка игры</div>

<script>
// Передаем instance_id из PHP в JavaScript
const currentInstanceId = <?= json_encode($instanceId) ?>;

// Глобальные переменные
let player = { 
    x: <?= $instance->x ?? 1.5 ?>, 
    y: <?= $instance->y ?? 1.5 ?>, 
    dir: <?= $instance->dir ?? 0 ?>, 
    cooldown: 0, 
    canMove: true 
};
let monsters = [];
let wallTexture = null;
let messageTimeout = null;
let isMoving = false;
let cooldownInterval = null;
let stepCounter = 0;

// Константы
const FOV = Math.PI / 2.5;
const NUM_RAYS = 320;

// URL текстуры стены
const WALL_TEXTURE_URL = '/img/wall.jpeg';

// Направления
const directions = [
    { name: "Восток →", dx: 1, dy: 0, angle3d: 0 },
    { name: "Юг ↓", dx: 0, dy: 1, angle3d: Math.PI / 2 },
    { name: "Запад ←", dx: -1, dy: 0, angle3d: Math.PI },
    { name: "Север ↑", dx: 0, dy: -1, angle3d: Math.PI * 1.5 }
];

let mapSize = 32;
let map = [];

const view = document.getElementById('view');
const mapDiv = document.getElementById('minimap');

console.log('Instance ID:', currentInstanceId);

// Функция загрузки изображения
function loadImage(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            console.log('✅ Изображение загружено:', url);
            resolve(img);
        };
        img.onerror = (error) => {
            console.error('❌ Ошибка загрузки:', url, error);
            reject(error);
        };
        img.src = url;
    });
}

// Создание текстуры стены
function createRealisticWallTexture() {
    const canvas = document.createElement('canvas');
    canvas.width = 256;
    canvas.height = 256;
    const ctx = canvas.getContext('2d');
    
    ctx.fillStyle = '#8B6914';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    for (let i = 0; i < 1000; i++) {
        ctx.fillStyle = `rgba(60, 40, 20, ${Math.random() * 0.4})`;
        ctx.fillRect(
            Math.random() * canvas.width,
            Math.random() * canvas.height,
            2 + Math.random() * 4,
            2 + Math.random() * 4
        );
    }
    
    ctx.strokeStyle = '#4A3520';
    ctx.lineWidth = 3;
    for (let y = 32; y < canvas.height; y += 32) {
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(canvas.width, y);
        ctx.stroke();
    }
    
    return canvas;
}

// Загрузка текстуры стены
async function loadWallTexture() {
    const previewDiv = document.getElementById('texturePreview');
    
    try {
        previewDiv.innerHTML = '🎨 Загрузка текстуры стен...';
        const texture = await loadImage(WALL_TEXTURE_URL);
        previewDiv.innerHTML = '✅ Текстура стен загружена!';
        showMessage("✅ Текстура стен загружена");
        return texture;
    } catch (error) {
        previewDiv.innerHTML = '🎨 Используется стандартная текстура стен';
        showMessage("⚠️ Текстура стен не загружена, используется стандартная", true);
        return createRealisticWallTexture();
    }
}

async function savePlayerData() {
    if (!currentInstanceId) {
        console.log('Нет instance_id, пропускаем сохранение');
        return;
    }
    
    const saveData = { 
        x: player.x, 
        y: player.y, 
        dir: player.dir 
    };
    console.log('Отправляем данные:', saveData);
    
    try {
        const response = await fetch('/dungeon/saveplayer', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(saveData)
        });
        const data = await response.json();
        console.log('Ответ сервера:', data);
        if (data.success) {
            console.log('Данные сохранены');
        } else {
            console.error('Ошибка сохранения:', data.error, data.errors);
        }
    } catch (error) {
        console.error('Ошибка сохранения:', error);
    }
}

// Загрузка данных игрока
async function loadPlayerData() {
    if (!currentInstanceId) return false;
    
    try {
        const response = await fetch(`/dungeon/loadplayer?instance_id=${currentInstanceId}`);
        const data = await response.json();
        if (data.success && data.data) {
            player.x = parseFloat(data.data.x);
            player.y = parseFloat(data.data.y);
            player.dir = parseInt(data.data.dir);
            console.log('Данные игрока загружены:', player);
            return true;
        }
    } catch (error) {
        console.error('Ошибка загрузки игрока:', error);
    }
    return false;
}

// Загрузка монстров
async function loadMonsters() {
    if (!currentInstanceId) return false;
    
    try {
        const response = await fetch(`/dungeon/loadmonsters?instance_id=${currentInstanceId}`);
        const data = await response.json();
        if (data.success && data.monsters) {
            monsters = data.monsters;
            document.getElementById('monsterCount').textContent = monsters.length;
            await loadMonsterTextures();
            console.log('Монстры загружены:', monsters.length);
            return true;
        }
    } catch (error) {
        console.error('Ошибка загрузки монстров:', error);
    }
    return false;
}

// Загрузка карты
async function loadMap() {
    if (!currentInstanceId) return false;
    
    try {
        const response = await fetch(`/dungeon/loadmap?instance_id=${currentInstanceId}`);
        const data = await response.json();
        if (data.success && data.map) {
            map = data.map;
            mapSize = map.length;
            console.log('Карта загружена, размер:', mapSize);
            return true;
        }
    } catch (error) {
        console.error('Ошибка загрузки карты:', error);
    }
    return false;
}

// Загрузка текстур для монстров
async function loadMonsterTextures() {
    const previewDiv = document.getElementById('texturePreview');
    let loadedCount = 0;
    
    for (const monster of monsters) {
        if (monster.textureUrl) {
            try {
                const texture = await loadImage(monster.textureUrl);
                monster.texture = texture;
                loadedCount++;
            } catch (error) {
                monster.texture = null;
                loadedCount++;
            }
        } else {
            monster.texture = null;
            loadedCount++;
        }
    }
    
    previewDiv.innerHTML = `✅ Загружено текстур монстров: ${loadedCount}`;
}

// Отображение мини-карты
function drawMap() {
    if (!map.length) return;
    
    const cellSize = 9;
    const canvas = document.createElement('canvas');
    canvas.width = mapSize * cellSize;
    canvas.height = mapSize * cellSize;
    const ctx = canvas.getContext('2d');
    
    for (let y = 0; y < mapSize; y++) {
        for (let x = 0; x < mapSize; x++) {
            if (map[y][x] === '#') {
                ctx.fillStyle = '#2c3e50';
                ctx.fillRect(x * cellSize, y * cellSize, cellSize - 1, cellSize - 1);
                ctx.fillStyle = '#34495e';
                ctx.fillRect(x * cellSize + 1, y * cellSize + 1, cellSize - 3, cellSize - 3);
            } else {
                ctx.fillStyle = '#bdc3c7';
                ctx.fillRect(x * cellSize, y * cellSize, cellSize - 1, cellSize - 1);
                ctx.fillStyle = '#95a5a6';
                ctx.fillRect(x * cellSize + 1, y * cellSize + 1, cellSize - 3, cellSize - 3);
            }
        }
    }
    
    monsters.forEach(monster => {
        const x = Math.floor(monster.x);
        const y = Math.floor(monster.y);
        
        ctx.fillStyle = '#e74c3c';
        ctx.beginPath();
        ctx.arc(x * cellSize + cellSize/2, y * cellSize + cellSize/2, cellSize/3, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.fillStyle = 'white';
        ctx.font = 'bold 7px monospace';
        ctx.fillText(monster.icon || '👾', x * cellSize + 2, y * cellSize + 7);
        
        const healthPercent = monster.health / monster.maxHealth;
        ctx.fillStyle = '#c0392b';
        ctx.fillRect(x * cellSize + 2, y * cellSize - 2, cellSize - 4, 2);
        ctx.fillStyle = '#27ae60';
        ctx.fillRect(x * cellSize + 2, y * cellSize - 2, (cellSize - 4) * healthPercent, 2);
    });
    
    const cellX = Math.floor(player.x);
    const cellY = Math.floor(player.y);
    const centerX = cellX * cellSize + cellSize/2;
    const centerY = cellY * cellSize + cellSize/2;
    const arrowSize = cellSize / 1.2;
    
    let arrowAngle;
    switch(player.dir) {
        case 0: arrowAngle = Math.PI / 2; break;
        case 1: arrowAngle = Math.PI; break;
        case 2: arrowAngle = -Math.PI / 2; break;
        case 3: arrowAngle = 0; break;
        default: arrowAngle = 0;
    }
    
    ctx.save();
    ctx.translate(centerX, centerY);
    ctx.rotate(arrowAngle);
    
    ctx.shadowBlur = 2;
    ctx.shadowColor = 'rgba(0,0,0,0.5)';
    
    ctx.fillStyle = '#2ecc71';
    ctx.beginPath();
    ctx.moveTo(0, -arrowSize);
    ctx.lineTo(-arrowSize/2, arrowSize/2);
    ctx.lineTo(0, arrowSize/4);
    ctx.lineTo(arrowSize/2, arrowSize/2);
    ctx.fill();
    
    ctx.strokeStyle = '#27ae60';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(0, -arrowSize);
    ctx.lineTo(-arrowSize/2, arrowSize/2);
    ctx.lineTo(0, arrowSize/4);
    ctx.lineTo(arrowSize/2, arrowSize/2);
    ctx.closePath();
    ctx.stroke();
    
    ctx.beginPath();
    ctx.arc(0, 0, arrowSize/4, 0, Math.PI * 2);
    ctx.fillStyle = '#27ae60';
    ctx.fill();
    
    ctx.restore();
    
    mapDiv.innerHTML = '';
    mapDiv.appendChild(canvas);
}

function canMove(dx, dy) {
    const newX = Math.floor(player.x + dx);
    const newY = Math.floor(player.y + dy);
    
    if (newX < 0 || newY < 0 || newX >= mapSize || newY >= mapSize) {
        return false;
    }
    
    if (map[newY][newX] === '#') {
        return false;
    }
    
    const monsterCollision = monsters.some(monster => 
        Math.floor(monster.x) === newX && Math.floor(monster.y) === newY
    );
    
    return !monsterCollision;
}

function startCooldown() {
    if (cooldownInterval) {
        clearInterval(cooldownInterval);
    }
    
    player.cooldown = 100;
    player.canMove = false;
    updateCooldownDisplay();
    
    cooldownInterval = setInterval(() => {
        if (player.cooldown > 0) {
            player.cooldown -= 2;
            updateCooldownDisplay();
            
            if (player.cooldown <= 0) {
                clearInterval(cooldownInterval);
                player.canMove = true;
                cooldownInterval = null;
                updateCooldownDisplay();
                showMessage("✅ Кулдаун закончен! Можно двигаться");
            }
        }
    }, 50);
}

function updateCooldownDisplay() {
    const percent = Math.max(0, player.cooldown);
    document.getElementById('cooldownFill').style.width = `${percent}%`;
    
    const fill = document.getElementById('cooldownFill');
    if (player.cooldown > 60) {
        fill.style.background = '#e74c3c';
    } else if (player.cooldown > 30) {
        fill.style.background = '#f39c12';
    } else {
        fill.style.background = '#2ecc71';
    }
}

async function tryMove(forward) {
    if (!player.canMove || isMoving) {
        if (!player.canMove) {
            showMessage("⏳ Подождите, кулдаун еще не прошел!", true);
        }
        return false;
    }
    
    const dir = directions[player.dir];
    const dx = dir.dx * (forward ? 1 : -1);
    const dy = dir.dy * (forward ? 1 : -1);
    
    if (canMove(dx, dy)) {
        isMoving = true;
        stepCounter++;
        
        const startX = player.x;
        const startY = player.y;
        const targetX = player.x + dx;
        const targetY = player.y + dy;
        const steps = 10;
        let step = 0;
        
        const moveInterval = setInterval(() => {
            step++;
            player.x = startX + (dx * step / steps);
            player.y = startY + (dy * step / steps);
            drawMap();
            drawView();
            
            if (step >= steps) {
                clearInterval(moveInterval);
                player.x = targetX;
                player.y = targetY;
                isMoving = false;
                drawMap();
                drawView();
            }
        }, 20);
        
        showMessage(forward ? "🚶 Вы сделали шаг вперед" : "🚶 Вы сделали шаг назад");
        
        startCooldown();
        await savePlayerData();
        updateStats();
        
        return true;
    } else {
        showMessage("🚫 Движение невозможно! Стена или монстр блокирует путь!", true);
        return false;
    }
}

async function turn(left) {
    if (isMoving || !player.canMove) {
        if (!player.canMove) {
            showMessage("⏳ Нельзя поворачиваться во время кулдауна!", true);
        }
        return;
    }
    
    if (left) {
        player.dir = (player.dir + 3) % 4;
        showMessage(`🔄 Поворот налево: ${directions[player.dir].name}`);
    } else {
        player.dir = (player.dir + 1) % 4;
        showMessage(`🔄 Поворот направо: ${directions[player.dir].name}`);
    }
    
    startCooldown();
    await savePlayerData();
    
    updateStats();
    drawMap();
    drawView();
}

function getHitType(angle) {
    let dist = 0;
    const stepSize = 0.05;
    
    while (dist < 25) {
        const testX = player.x + Math.cos(angle) * dist;
        const testY = player.y + Math.sin(angle) * dist;
        
        if (testX < 0 || testY < 0 || testX >= mapSize || testY >= mapSize) {
            return { type: 'wall', dist: 25 };
        }
        
        if (map[Math.floor(testY)][Math.floor(testX)] === '#') {
            return { type: 'wall', dist: dist };
        }
        
        const monster = monsters.find(m => 
            Math.hypot(testX - m.x, testY - m.y) < 0.4
        );
        
        if (monster) {
            return { type: 'monster', dist: dist, monster: monster };
        }
        
        dist += stepSize;
    }
    return { type: 'wall', dist: 25 };
}

// Отрисовка стен - каждая колонна получает часть из общей текстуры стены
// Отрисовка стен - простой и надежный вариант
function drawWalls(ctx, viewWidth, viewHeight) {
    for (let i = 0; i < NUM_RAYS; i++) {
        const rayAngle = directions[player.dir].angle3d - FOV / 2 + (i / NUM_RAYS) * FOV;
        const hit = getHitType(rayAngle);
        
        // Исправление fish-eye эффекта
        const correctedDist = hit.dist * Math.cos(rayAngle - directions[player.dir].angle3d);
        
        // Высота стены обратно пропорциональна расстоянию (ПЕРСПЕКТИВА)
        let wallHeight = viewHeight / Math.max(0.1, correctedDist) * 0.8;
        wallHeight = Math.min(wallHeight, viewHeight);
        
        const topOffset = (viewHeight - wallHeight) / 2;
        const columnWidth = Math.ceil(viewWidth / NUM_RAYS);
        const xPos = i * columnWidth;
        
        if (hit.type === 'wall') {
            const brightness = Math.max(0.3, 1 - correctedDist / 25);
            
            const columnCanvas = document.createElement('canvas');
            columnCanvas.width = columnWidth;
            columnCanvas.height = wallHeight;
            const columnCtx = columnCanvas.getContext('2d');
            
            // Растягиваем текстуру на всю высоту колонны
            // Для непрерывности по горизонтали - сдвигаем текстуру
            const texX = (i * wallTexture.width / NUM_RAYS) % wallTexture.width;
            const texW = Math.min(wallTexture.width - texX, columnWidth);
            
            columnCtx.drawImage(wallTexture,
                texX, 0, texW, wallTexture.height,
                0, 0, texW, wallHeight
            );
            
            // Если не хватило текстуры, рисуем с начала
            if (texW < columnWidth) {
                columnCtx.drawImage(wallTexture,
                    0, 0, columnWidth - texW, wallTexture.height,
                    texW, 0, columnWidth - texW, wallHeight
                );
            }
            
            // Затемнение
            columnCtx.fillStyle = `rgba(0, 0, 0, ${1 - brightness})`;
            columnCtx.fillRect(0, 0, columnWidth, wallHeight);
            
            ctx.drawImage(columnCanvas, xPos, topOffset, columnWidth, wallHeight);
        }
    }
}
// Отрисовка монстров
function drawMonsters(ctx, viewWidth, viewHeight) {
    const visibleMonsters = [];
    
    for (const monster of monsters) {
        const dx = monster.x - player.x;
        const dy = monster.y - player.y;
        const dist = Math.hypot(dx, dy);
        
        if (dist < 10) {
            const angleToMonster = Math.atan2(dy, dx);
            let angleDiff = angleToMonster - directions[player.dir].angle3d;
            
            while (angleDiff > Math.PI) angleDiff -= Math.PI * 2;
            while (angleDiff < -Math.PI) angleDiff += Math.PI * 2;
            
            if (Math.abs(angleDiff) < FOV / 2) {
                const hit = getHitType(angleToMonster);
                if (hit.type === 'monster' && hit.monster === monster) {
                    visibleMonsters.push({
                        monster: monster,
                        dist: dist,
                        angleDiff: angleDiff
                    });
                }
            }
        }
    }
    
    visibleMonsters.sort((a, b) => b.dist - a.dist);
    
    for (const vm of visibleMonsters) {
        const monster = vm.monster;
        const dist = vm.dist;
        const angleDiff = vm.angleDiff;
        
        const correctedDist = dist * Math.cos(angleDiff);
        let monsterHeight = viewHeight / Math.max(0.1, correctedDist) * 0.8;
        monsterHeight = Math.min(monsterHeight, viewHeight);
        
        const topOffset = (viewHeight - monsterHeight) / 2;
        
        const screenX = (angleDiff / FOV + 0.5) * viewWidth;
        const monsterWidth = monsterHeight * 0.5;
        const left = screenX - monsterWidth / 2;
        
        if (left + monsterWidth > 0 && left < viewWidth) {
            const brightness = Math.max(0.4, 1 - dist / 15);
            
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = monsterWidth;
            tempCanvas.height = monsterHeight;
            const tempCtx = tempCanvas.getContext('2d');
            
            if (monster.texture) {
                tempCtx.drawImage(monster.texture,
                    0, 0, monster.texture.width, monster.texture.height,
                    0, 0, monsterWidth, monsterHeight
                );
            } else {
                tempCtx.fillStyle = monster.color || '#8B7355';
                tempCtx.fillRect(0, 0, monsterWidth, monsterHeight);
            }
            
            tempCtx.fillStyle = `rgba(0, 0, 0, ${1 - brightness + 0.2})`;
            tempCtx.fillRect(0, 0, monsterWidth, monsterHeight);
            
            tempCtx.fillStyle = `rgba(255, 255, 255, 0.9)`;
            tempCtx.font = `bold ${Math.max(12, Math.floor(monsterWidth * 0.12))}px monospace`;
            tempCtx.fillText(monster.name, monsterWidth * 0.1, monsterHeight * 0.12);
            
            const healthPercent = monster.health / monster.maxHealth;
            tempCtx.fillStyle = '#c0392b';
            tempCtx.fillRect(monsterWidth * 0.1, monsterHeight * 0.85, monsterWidth * 0.8, 6);
            tempCtx.fillStyle = '#27ae60';
            tempCtx.fillRect(monsterWidth * 0.1, monsterHeight * 0.85, monsterWidth * 0.8 * healthPercent, 6);
            
            tempCtx.fillStyle = 'white';
            tempCtx.font = `${Math.max(10, Math.floor(monsterWidth * 0.08))}px monospace`;
            tempCtx.fillText(`${monster.health}/${monster.maxHealth}`, 
                            monsterWidth * 0.1 + monsterWidth * 0.4, monsterHeight * 0.85 - 2);
            
            ctx.drawImage(tempCanvas, left, topOffset, monsterWidth, monsterHeight);
        }
    }
}

// Небо и пол
function drawSkyAndGround(ctx, viewWidth, viewHeight) {
    const skyGradient = ctx.createLinearGradient(0, 0, 0, viewHeight/2);
    skyGradient.addColorStop(0, '#0a0a2a');
    skyGradient.addColorStop(0.5, '#1a1a4e');
    skyGradient.addColorStop(1, '#2a1a5e');
    ctx.fillStyle = skyGradient;
    ctx.fillRect(0, 0, viewWidth, viewHeight/2);
    
    for (let i = 0; i < 100; i++) {
        const x = (i * 137 + stepCounter * 10) % viewWidth;
        const y = (i * 73) % (viewHeight/2);
        ctx.fillStyle = `rgba(255, 255, 255, 0.5)`;
        ctx.fillRect(x, y, 1, 1);
    }
    
    const groundGradient = ctx.createLinearGradient(0, viewHeight/2, 0, viewHeight);
    groundGradient.addColorStop(0, '#2a1a0a');
    groundGradient.addColorStop(0.5, '#1a0a00');
    groundGradient.addColorStop(1, '#0a0000');
    ctx.fillStyle = groundGradient;
    ctx.fillRect(0, viewHeight/2, viewWidth, viewHeight/2);
}

// Основная 3D отрисовка
function drawView() {
    if (!wallTexture || !map.length) return;
    
    const viewWidth = view.clientWidth;
    const viewHeight = view.clientHeight;
    
    const canvas = document.createElement('canvas');
    canvas.width = viewWidth;
    canvas.height = viewHeight;
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    const ctx = canvas.getContext('2d');
    
    drawSkyAndGround(ctx, viewWidth, viewHeight);
    drawWalls(ctx, viewWidth, viewHeight);
    drawMonsters(ctx, viewWidth, viewHeight);
    
    view.innerHTML = '';
    view.appendChild(canvas);
}

function updateStats() {
    document.getElementById('posX').textContent = Math.floor(player.x);
    document.getElementById('posY').textContent = Math.floor(player.y);
    document.getElementById('direction').textContent = directions[player.dir].name;
    document.getElementById('monsterCount').textContent = monsters.length;
}

function showMessage(text, isError = false) {
    const oldMessage = document.querySelector('.message');
    if (oldMessage) oldMessage.remove();
    if (messageTimeout) clearTimeout(messageTimeout);
    
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message';
    messageDiv.style.color = isError ? '#e74c3c' : '#f1c40f';
    messageDiv.textContent = text;
    document.body.appendChild(messageDiv);
    
    messageTimeout = setTimeout(() => {
        if (messageDiv) messageDiv.remove();
    }, 3000);
}

function checkMonsterClick(clientX, clientY) {
    const rect = view.getBoundingClientRect();
    const x = (clientX - rect.left) / rect.width;
    
    const rayIndex = Math.floor(x * NUM_RAYS);
    
    if (rayIndex >= 0 && rayIndex < NUM_RAYS) {
        const rayAngle = directions[player.dir].angle3d - FOV / 2 + (rayIndex / NUM_RAYS) * FOV;
        const hit = getHitType(rayAngle);
        
        if (hit.type === 'monster' && hit.dist < 8) {
            fightMonster(hit.monster.id);
            return true;
        } else if (hit.type === 'monster') {
            showMessage(`👾 ${hit.monster.name} слишком далеко для атаки!`, true);
        }
    }
    return false;
}

function fightMonster(monsterId) {
    window.location.href = `/dungeon/battle?monster_id=${monsterId}`;
}

// Инициализация игры
async function init() {
    console.log('Инициализация игры...');
    
    if (!currentInstanceId) {
        console.error('No instance_id provided');
        const loadingDiv = document.getElementById('loading');
        if (loadingDiv) {
            loadingDiv.innerHTML = 'Ошибка: не указан ID инстанса. Перезагрузите страницу.';
        }
        return;
    }
    
    await loadMap();
    await loadPlayerData();
    await loadMonsters();
    wallTexture = await loadWallTexture();
    
    setTimeout(() => {
        drawMap();
        drawView();
        updateStats();
        player.canMove = true;
        player.cooldown = 0;
        updateCooldownDisplay();
    }, 100);
    
    const loadingDiv = document.getElementById('loading');
    if (loadingDiv) {
        loadingDiv.style.display = 'none';
    }
    showMessage("✨ Добро пожаловать в лабиринт! ✨");
}

// Обработчики событий
document.addEventListener('keydown', (e) => {
    if (isMoving) return;
    
    switch(e.key) {
        case 'ArrowUp': e.preventDefault(); tryMove(true); break;
        case 'ArrowDown': e.preventDefault(); tryMove(false); break;
        case 'ArrowLeft': e.preventDefault(); turn(true); break;
        case 'ArrowRight': e.preventDefault(); turn(false); break;
    }
});

view.addEventListener('click', (e) => {
    if (!player.canMove) {
        showMessage("⏳ Нельзя атаковать во время кулдауна!", true);
        return;
    }
    checkMonsterClick(e.clientX, e.clientY);
});

// Запуск игры
init();

// Автосохранение каждые 5 секунд
setInterval(() => { if (!isMoving) savePlayerData(); }, 5000);
</script>
</body>
</html>