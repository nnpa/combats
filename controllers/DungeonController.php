<?php

namespace app\controllers;
use Yii;
use app\models\User;
use app\models\Instance;
use app\models\InstanceBots;
use yii\web\Response;
use app\models\Battle;
use app\models\UserBattle;
use app\models\BattleAttack;
use app\models\BattleLog;

use app\models\UserSpells;
use app\models\Spells;

class DungeonController extends AppController
{
    public $enableCsrfValidation = false;
    
    /**
     * Сохранение данных игрока
     */
    public function actionSaveplayer()
{
    // Устанавливаем JSON формат ответа
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    // Получаем данные из POST запроса
    $request = Yii::$app->request;
    $data = json_decode($request->getRawBody(), true);
    
    // Логируем полученные данные для отладки
    error_log('Saveplayer data: ' . print_r($data, true));
    
    // Проверяем наличие данных
    if (!$data || !isset($data['x']) || !isset($data['y']) || !isset($data['dir'])) {
        return [
            'success' => false,
            'error' => 'Invalid data'
        ];
    }
    
    // Получаем ID пользователя
    $userId = Yii::$app->user->id;
    
    if (!$userId) {
        return [
            'success' => false,
            'error' => 'User not authenticated'
        ];
    }
    
    // Находим активный инстанс игры пользователя
    $instance = Instance::find()
        ->where(['user_id' => $userId])
        ->orderBy(['id' => SORT_DESC])
        ->one();
    
    if (!$instance) {
        return [
            'success' => false,
            'error' => 'Game instance not found'
        ];
    }
    
    // Обновляем данные игрока
    $instance->x = (string)$data['x'];
    $instance->y = (string)$data['y'];
    $instance->dir = (int)$data['dir'];
    
    // Сохраняем и проверяем результат
    if ($instance->save(false)) {
        return [
            'success' => true,
            'message' => 'Player data saved successfully'
        ];
    } else {
        // Возвращаем ошибки валидации
        return [
            'success' => false,
            'error' => 'Failed to save data',
            'errors' => $instance->errors
        ];
    }
}
    
    public function actionLoadplayer()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    $instanceId = Yii::$app->request->get('instance_id');
    
    if (!$instanceId) {
        return [
            'success' => false,
            'error' => 'Instance ID required'
        ];
    }
    
    $instance = Instance::findOne($instanceId);
    
    if (!$instance) {
        return [
            'success' => false,
            'error' => 'Instance not found'
        ];
    }
    
    return [
        'success' => true,
        'data' => [
            'x' => (float)$instance->x,
            'y' => (float)$instance->y,
            'dir' => (int)$instance->dir,
            'instance_id' => $instance->id
        ]
    ];
}
    
    /**
     * Загрузка монстров для инстанса
     */
    public function actionLoadmonsters()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $instanceId = Yii::$app->request->get('instance_id');
        
        if (!$instanceId) {
            return [
                'success' => false,
                'error' => 'Instance ID required'
            ];
        }
        
        $monsters = InstanceBots::find()
            ->where(['instance_id' => $instanceId])
            ->all();
        
        $result = [];
        foreach ($monsters as $monster) {
            $result[] = [
                'id' => $monster->id,
                'bot_id' => $monster->bot_id,
                'x' => (float)$monster->x,
                'y' => (float)$monster->y,
                'health' => $monster->health,
                'maxHealth' => $monster->maxHelth,
                'name' => $monster->name,
                'color' => $monster->color,
                'textureUrl' => $monster->textureUrl
            ];
        }
        
        return [
            'success' => true,
            'monsters' => $result
        ];
    }
    
    /**
     * Загрузка карты для инстанса
     */
    public function actionLoadmap()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $instanceId = Yii::$app->request->get('instance_id');
        
        if (!$instanceId) {
            return [
                'success' => false,
                'error' => 'Instance ID required'
            ];
        }
        
        $instance = Instance::findOne($instanceId);
        
        if (!$instance) {
            return [
                'success' => false,
                'error' => 'Instance not found'
            ];
        }
        
        $map = json_decode($instance->map, true);
        
        return [
            'success' => true,
            'map' => $map
        ];
    }
    
    /**
     * Выход из подземелья
     */
    public function actionExit()
    {
        $this->layout = false;
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        $instanceId = $user->instance_id;
        
        $user->instance_id = null;
        $user->save(false);
        
        // Количество оставшихся ботов
        $remainingBots = InstanceBots::find()->where(["instance_id" => $instanceId])->count();
        
        // Шанс выпадения спелла (20 - сколько ботов осталось)
        $chance = 20 - $remainingBots;
        
        // Кидаем кубик от 1 до 100 (шанс в процентах)
        $roll = mt_rand(1, 100);
        
        // Проверяем, сработал ли шанс
        if ($roll <= $chance) {
            // Даём спелл заточки с id = 1
            $userSpell = new UserSpells();
            $userSpell->user_id = $user->id;
            $userSpell->spell_id = 1;
            $userSpell->save(false);
            
            Yii::$app->session->setFlash('success', 'Вам выпал спелл заточки!');
        }
        
        // Расчёт репутации в геометрической прогрессии
        $killedBots = 20 - $remainingBots;
        
        $baseReputation = 1;
        $multiplier = 2;
        $reputationGain = $baseReputation * pow($multiplier, $killedBots);
        
        $user->repa = $user->repa + $reputationGain;
        $user->save(false);
        
        // Удаляем инстанс и ботов
        Instance::deleteAll(["id" => $instanceId]);
        InstanceBots::deleteAll(["instance_id" => $instanceId]);
        
        return $this->redirect("/site/cp");
    }
    
    /**
     * Главная страница подземелья
     */
    public function actionIndex()
    {   
        $this->layout = false;
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        if(is_null($user->instance_id)){
            return;
        }
        
        $instance = Instance::findOne(["id" => $user->instance_id]);
        $instanceBots = InstanceBots::findAll(["instance_id" => $user->instance_id]);
        
        return $this->render('index', [
            "instance" => $instance,
            "instanceBots" => $instanceBots,
            "instanceId" => $instance->id
        ]);
    }
    
    /**
     * Старт новой игры
     */
    /**
 * Старт новой игры
 */
public function actionStart()
{
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    $userId = $user->id;
    
    if($user->instance_id != null){
        return $this->redirect("/dungeon/index");
    }
    
    // Генерируем случайную карту 32x32
    $map = $this->generateRandomMap(32, 32);
    
    // Начальные координаты игрока
    $startX = 1.5;
    $startY = 1.5;
    $startDir = 0;
    
    // Создаем экземпляр игры
    $instance = new Instance();
    $instance->user_id = $userId;
    $instance->level = $user->level;
    $instance->map = json_encode($map);
    $instance->x = $startX;
    $instance->y = $startY;
    $instance->dir = $startDir;
    $instance->cooldown = 0;
    $instance->canMove = 1;
    $instance->save(false);

    $user->instance_id = $instance->id;
    $user->save(false);
    
    // Генерируем и расставляем монстров (20 штук)
    $monsters = $this->generateMonsters($map, 20, $instance->id);
    
    foreach ($monsters as $monster) {
        $instanceBot = new InstanceBots();
        $instanceBot->bot_id = $monster['bot_id'];
        $instanceBot->x = $monster['x'];
        $instanceBot->y = $monster['y'];
        $instanceBot->health = $monster['health'];
        $instanceBot->maxHelth = $monster['maxHealth'];
        $instanceBot->name = $monster['name'];
        $instanceBot->color = $monster['color'];
        $instanceBot->textureUrl = $monster['textureUrl'];
        $instanceBot->instance_id = $instance->id;
        
        $instanceBot->save(false);
    }
    
    return $this->redirect("/dungeon/index?instance_id=" . $instance->id);
}
    
    /**
     * Генерация и расстановка монстров на карте
     * @param array $map Карта лабиринта
     * @param int $count Количество монстров
     * @param int $instanceId ID инстанса
     * @return array Массив монстров
     */
    private function generateMonsters($map, $count, $instanceId)
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        $monsters = [];
        $height = count($map);
        $width = strlen($map[0]);
        
        // Собираем все свободные клетки (где можно разместить монстра)
        $freeCells = [];
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($map[$y][$x] === '.') {
                    $freeCells[] = ['x' => $x, 'y' => $y];
                }
            }
        }
        
        // Исключаем стартовую позицию (1,1)
        $freeCells = array_filter($freeCells, function($cell) {
            return !($cell['x'] == 1 && $cell['y'] == 1);
        });
        $freeCells = array_values($freeCells);
        
        // Перемешиваем клетки
        shuffle($freeCells);
        
        // Берем первые $count клеток
        $selectedCells = array_slice($freeCells, 0, $count);
        
        // Данные монстра в зависимости от уровня
        if($user->level == 1){
            $monsterData = [
                'bot_id' => 6,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Скелет',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/skeleton.png'
            ];
        } elseif($user->level == 2){
            $monsterData = [
                'bot_id' => 7,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Зомби',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/zombie.png'
            ];
        } elseif($user->level == 3){
            $monsterData = [
                'bot_id' => 8,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Гоблин',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/goblin.png'
            ];
        } elseif($user->level == 4){
            $monsterData = [
                'bot_id' => 9,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Гарпия',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/harpy.png'
            ];
        } elseif($user->level == 5){
            $monsterData = [
                'bot_id' => 10,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Голем',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/golem.png'
            ];
        } elseif($user->level == 6){
            $monsterData = [
                'bot_id' => 11,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Вампир',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/vampire.png'
            ];
        } elseif($user->level == 7){
            $monsterData = [
                'bot_id' => 12,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Дух',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/spirit.png'
            ];
        } elseif($user->level == 8){
            $monsterData = [
                'bot_id' => 13,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Дракон',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/dragon.png'
            ];
        } elseif($user->level == 9){
            $monsterData = [
                'bot_id' => 14,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Демон',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/demon.png'
            ];
        } else {
            $monsterData = [
                'bot_id' => 15,
                'health' => 50,
                'maxHealth' => 50,
                'name' => 'Лич',
                'color' => '#cdcdcd',
                'textureUrl' => '/img/monstr/lich.png'
            ];
        }
        
        // Создаем монстров
        foreach ($selectedCells as $index => $cell) {
            $monsters[] = [
                'id' => $index + 1,
                'bot_id' => $monsterData['bot_id'],
                'x' => $cell['x'] + 0.5,
                'y' => $cell['y'] + 0.5,
                'health' => $monsterData['health'],
                'maxHealth' => $monsterData['maxHealth'],
                'name' => $monsterData['name'],
                'color' => $monsterData['color'],
                'textureUrl' => $monsterData['textureUrl'],
                'instance_id' => $instanceId
            ];
        }
        
        return $monsters;
    }
    
    /**
     * Битва с монстром
     */
    public function actionBattle($monster_id)
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);

        // Проверка, нет ли уже активной битвы
        if (!is_null($user->battle_id) && $user->in_battle == 1) {
            Yii::$app->session->setFlash('error', 'У вас уже есть активный бой!');
            return $this->redirect("/battle/index");
        }

        // Получаем данные монстра (бота)
        $instanceBot = InstanceBots::findOne(["id" => $monster_id]);
        if (!$instanceBot) {
            Yii::$app->session->setFlash('error', 'Монстр не найден!');
            return $this->redirect("/dungeon/index");
        }

        $bot = User::findOne(["id" => $instanceBot->bot_id]);
        if (!$bot) {
            Yii::$app->session->setFlash('error', 'Данные монстра не найдены!');
            return $this->redirect("/dungeon/index");
        }

        // Создаем битву
        $battle = new Battle();
        $battle->start_time = time();
        $battle->type = 1;
        $battle->level = $user->level;
        $battle->started = 1;
        $battle->save(false);

        // Обновляем данные пользователя
        $user->battle_id = $battle->id;
        $user->in_battle = 1;
        $user->save(false);

        // СОЗДАЕМ УЧАСТНИКА - ИГРОК
        $userBattle = new UserBattle();
        $userBattle->battle_id = $battle->id;
        $userBattle->user_id = $user->id;
        $userBattle->bot_id = null;
        $userBattle->hp = $user->health;
        $userBattle->user_session = $user->session_id;
        $userBattle->komand = 1;
        $userBattle->priority = 1;
        $userBattle->IsAlive = 1;
        $userBattle->shild = 0;
        $userBattle->total_damage = 0;
        $userBattle->save(false);

        // СОЗДАЕМ УЧАСТНИКА - БОТ
        $botBattle = new UserBattle();
        $botBattle->battle_id = $battle->id;
        $botBattle->user_id = null;
        $botBattle->bot_id = $instanceBot->bot_id;
        $botBattle->hp = $bot->health;
        $botBattle->user_session = null;
        $botBattle->komand = 2;
        $botBattle->priority = 1;
        $botBattle->IsAlive = 1;
        $botBattle->shild = 0;
        $botBattle->total_damage = 0;
        $botBattle->save(false);

        // Устанавливаем цели
        $userBattle->target = $botBattle->priority;
        $userBattle->save(false);

        $botBattle->target = $userBattle->priority;
        $botBattle->save(false);

        $instanceBot->delete();

        return $this->redirect("/battle/battle");
    }

    /**
 * Генерация случайной карты лабиринта
 * @param int $width Ширина карты
 * @param int $height Высота карты
 * @return array Массив строк карты
 */
private function generateRandomMap($width = 32, $height = 32)
{
    // Инициализируем карту стенами
    $map = array_fill(0, $height, str_repeat('#', $width));
    
    // Делаем проходы (начинаем с позиции 1,1)
    $this->carveMaze($map, 1, 1, $width, $height);
    
    // Добавляем комнаты
    $this->addRooms($map, $width, $height);
    
    // Добавляем дополнительные проходы
    $this->addExtraPassages($map, $width, $height);
    
    // Убеждаемся, что края карты - стены
    for ($x = 0; $x < $width; $x++) {
        $map[0] = substr_replace($map[0], '#', $x, 1);
        $map[$height - 1] = substr_replace($map[$height - 1], '#', $x, 1);
    }
    for ($y = 0; $y < $height; $y++) {
        $map[$y] = substr_replace($map[$y], '#', 0, 1);
        $map[$y] = substr_replace($map[$y], '#', $width - 1, 1);
    }
    
    // Убеждаемся, что стартовая позиция проходима
    $map[1] = substr_replace($map[1], '.', 1, 1);
    
    return $map;
}

/**
 * Рекурсивное создание проходов в лабиринте
 */
private function carveMaze(&$map, $x, $y, $width, $height)
{
    $directions = [
        [0, -2, 0, -1], // вверх
        [0, 2, 0, 1],   // вниз
        [-2, 0, -1, 0], // влево
        [2, 0, 1, 0]    // вправо
    ];
    
    shuffle($directions);
    
    foreach ($directions as $dir) {
        $newX = $x + $dir[0];
        $newY = $y + $dir[1];
        $wallX = $x + $dir[2];
        $wallY = $y + $dir[3];
        
        if ($newX > 0 && $newX < $width - 1 && $newY > 0 && $newY < $height - 1 && $map[$newY][$newX] === '#') {
            $map[$newY] = substr_replace($map[$newY], '.', $newX, 1);
            $map[$wallY] = substr_replace($map[$wallY], '.', $wallX, 1);
            $this->carveMaze($map, $newX, $newY, $width, $height);
        }
    }
}

/**
 * Добавление комнат для разнообразия
 */
private function addRooms(&$map, $width, $height)
{
    $numRooms = rand(5, 10);
    
    for ($i = 0; $i < $numRooms; $i++) {
        $roomWidth = rand(3, 6);
        $roomHeight = rand(3, 6);
        $roomX = rand(2, $width - $roomWidth - 2);
        $roomY = rand(2, $height - $roomHeight - 2);
        
        $valid = true;
        for ($y = $roomY; $y < $roomY + $roomHeight; $y++) {
            for ($x = $roomX; $x < $roomX + $roomWidth; $x++) {
                if ($map[$y][$x] !== '.' && $map[$y][$x] !== '#') {
                    $valid = false;
                    break;
                }
            }
        }
        
        if ($valid) {
            for ($y = $roomY; $y < $roomY + $roomHeight; $y++) {
                for ($x = $roomX; $x < $roomX + $roomWidth; $x++) {
                    $map[$y] = substr_replace($map[$y], '.', $x, 1);
                }
            }
        }
    }
}

/**
 * Добавление дополнительных проходов для устранения тупиков
 */
private function addExtraPassages(&$map, $width, $height)
{
    for ($y = 2; $y < $height - 2; $y++) {
        for ($x = 2; $x < $width - 2; $x++) {
            if ($map[$y][$x] === '#') {
                $passages = 0;
                if ($map[$y - 1][$x] === '.') $passages++;
                if ($map[$y + 1][$x] === '.') $passages++;
                if ($map[$y][$x - 1] === '.') $passages++;
                if ($map[$y][$x + 1] === '.') $passages++;
                
                if ($passages >= 3 && rand(1, 100) > 70) {
                    $map[$y] = substr_replace($map[$y], '.', $x, 1);
                }
            }
        }
    }
}
}