<?php

namespace app\controllers;
use Yii;
use app\models\User;
use app\models\Battle;
use app\models\UserBattle;
use app\models\BattleAttack;
use app\models\BattleLog;
use yii\data\Pagination;  // <-- ДОБАВЬТЕ ЭТУ СТРОКУ

class BattleController extends AppController
{
    //страница поединки
public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        
        //если отправлена форма заявки на бой
        if(isset($_POST["count"]) && is_null($user->battle_id)){
            $count = $_POST["count"];
            if($count != 1 && $count != 3){ // Исправлено: && вместо ||
                $count = 1;
            }
            $battle = new Battle();
            $battle->start_time = time() + 60;
            $battle->type = $count;
            $battle->level = $user->level;
            $battle->save(false);
            
            $user->battle_id = $battle->id;
            $user->save(false);
            
            $userBattle = new UserBattle();
            $userBattle->battle_id = $battle->id;
            $userBattle->user_id = $user->id;
            $userBattle->hp = $user->health;
            $userBattle->user_session = $user->session_id;
            $userBattle->save(false);
        }
        
        //если противник присоединился к бою
        if(isset($_POST["battle_id"]) && is_null($user->battle_id)){
            $id = $_POST["battle_id"];
            
            // Проверяем, есть ли место в бою
            $battle = Battle::findOne($id);
            if($battle && $battle->started === null) {
                $participantsCount = UserBattle::find()
                    ->where(['battle_id' => $id])
                    ->count();
                
                $maxPlayers = $battle->type * 2;
                
                if($participantsCount < $maxPlayers) {
                    $user->battle_id = $id;
                    $user->save(false);
                    
                    $userBattle = new UserBattle();
                    $userBattle->battle_id = $id;
                    $userBattle->user_id = $user->id;
                    $userBattle->hp = $user->health;
                    $userBattle->user_session = $user->session_id;
                    $userBattle->save(false);
                }
            }
        }
        
        //поиск всех битв, которые еще не начались
        $battles = Battle::find()
            ->where(['started' => null])
            ->andWhere(['>', 'start_time', time()])
            ->all();
        
        return $this->render('zayavka',["user" => $user,"battles"=>$battles]);
    }
    //основной бой
    public function actionBattle()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        //если бой уже завершен редирект на заявки
        if(is_null($user->in_battle)){
            return $this->redirect("/battle/index");
        }
        
        //получить противника
        $enemy = $this->getEnemy($user->id,$user->battle_id);   
       
        //вывод логов
        $logs = BattleLog::find()
            ->where(['battle_id' => $user->battle_id])
            ->orderBy(['attack_time' => SORT_DESC])
            ->all();
        //получение пользователя из таблицы UserBattle
        $battleUser  = UserBattle::find()->where(['user_id' => $user->id,"battle_id" => $user->battle_id])->one();
        $battleEnemy = null;
        //если враг не пустой
        if(!is_null($enemy)){
            $bot = UserBattle::find()->where(['bot_id' => $enemy->id,"battle_id" => $user->battle_id])->one();
            if(!is_null($bot)){
               $battleEnemy =  $bot;
            }else{
                $battleEnemy = UserBattle::find()->where(['user_id' => $enemy->id,"battle_id" => $user->battle_id])->one();
            }
        }
        
        return $this->render('index',["user" => $user,"enemy" =>$enemy,"logs" => $logs,"battleUser" =>$battleUser,"battleEnemy"=>$battleEnemy]);
    }
    
    public function getEnemy($userId,$battleId){
        $userBattle = UserBattle::findOne(["user_id" => $userId,"battle_id" =>$battleId]);
        $team = $userBattle->komand;
        
        if($team == 1){
            $team =2;
        }else{
            $team = 1;
        }
        
        if(!is_null($userBattle->target)){
            $enemyUserBattle = UserBattle::findOne(["komand"=>$team,"priority"=>$userBattle->target,"battle_id" => $userBattle->battle_id,"IsAlive" => 1]);
            if(is_null($enemyUserBattle)){
                
                $enemyId = $this->nextTarget($team, $userBattle->battle_id, $userId);
                $enemyUserBattle = UserBattle::findOne(["komand"=>$team,"battle_id" => $userBattle->battle_id]);

            }
            if(is_null($enemyUserBattle->user_id)){
                $enemyId = $enemyUserBattle->bot_id;
            }else{
                $enemyId = $enemyUserBattle->user_id;
            }
        }else{
            $enemyId = $this->nextTarget($team, $userBattle->battle_id, $userId);
        }
        
        
        $enemy = User::findOne(["id" => $enemyId]);
        
        return $enemy;
    }
    
    public function nextTarget($team,$battleId,$userId){
        $BattleUsers = UserBattle::findAll(["battle_id" => $battleId,"komand"=>$team,"IsAlive" => 1]);
            
        $nextTarget = false;
        
        foreach($BattleUsers as $e){
            if(is_null($e->user_id)){
                $enemyId = $e->bot_id;
            }else{
                $enemyId = $e->user_id;
            }
            $notAttack = BattleAttack::findOne(["user_id" => $userId,"enemy_id" => $enemyId]);

            if(is_null($notAttack)){
                $nextTarget = true;
                $enemyUserBattle = UserBattle::find()->where(['user_id' => $enemyId])->orWhere(['bot_id' => $enemyId])->one();
            }
        }
        if(!$nextTarget){
            $enemyUserBattle = null;
            $enemyId = 0;
        } else{
            if(is_null($enemyUserBattle->user_id)){
                $enemyId = $enemyUserBattle->bot_id;
            }else{
                $enemyId = $enemyUserBattle->user_id;
            }
        }
        return $enemyId;
    }
    
    public function actionSkill($id,$defence,$skill){
    $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        
        $battleAttack = new BattleAttack();
        $battleAttack->battle_id = $user->battle_id;
        $battleAttack->user_id = $user->id;
        $battleAttack->enemy_id = $id;
        $battleAttack->skill = $skill;
        $battleAttack->block = $defence;
        $battleAttack->attack_time = time();

        $battleAttack->save(false);
        
        $userBattle = UserBattle::findOne(["user_id" => $user->id,"battle_id" =>$user->battle_id]);
        $team = $userBattle->komand;
        
        if($team == 1){
            $team =2;
        }else{
            $team = 1;
        }
        
        $bot = UserBattle::find()->where(['bot_id' => $id,"battle_id" =>$user->battle_id])->one();
        if(!is_null($bot)){
            $this->botAttack($userBattle,$bot);
        }
        
        $enemyId = $this->nextTarget($team, $user->battle_id, $user->id);
        
        $enemyUserBattle = UserBattle::find()->where(['user_id' => $enemyId])->orWhere(['bot_id' => $enemyId])->one();


        
        if(!is_null($enemyUserBattle)){
            $userBattle->target = $enemyUserBattle->priority;
        }else{
            $userBattle->target = null;
        }
        
        $userBattle->save(false);
    }
    
    public function botAttack($userBattle,$enemyUserBattle){
        $battleAttack = new BattleAttack();
        $battleAttack->battle_id = $userBattle->battle_id;
        $battleAttack->user_id = $enemyUserBattle->bot_id;
        $battleAttack->enemy_id = $userBattle->user_id;
        $battleAttack->attack =  rand(1, 4);
        $battleAttack->block =  rand(1, 4);
        $battleAttack->isBot =  1;

        $battleAttack->save(false);
    }
    
    public function actionAttack($id,$defence,$attack){
        
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        
        $battleAttack = new BattleAttack();
        $battleAttack->battle_id = $user->battle_id;
        $battleAttack->user_id = $user->id;
        $battleAttack->enemy_id = $id;
        $battleAttack->attack = $attack;
        $battleAttack->block = $defence;
        $battleAttack->attack_time = time();

        $battleAttack->save(false);
        
        $userBattle = UserBattle::findOne(["user_id" => $user->id,"battle_id" =>$user->battle_id]);
        $team = $userBattle->komand;
        
        if($team == 1){
            $team =2;
        }else{
            $team = 1;
        }
        
        $bot = UserBattle::find()->where(['bot_id' => $id,"battle_id" =>$user->battle_id])->one();
        if(!is_null($bot)){
            $this->botAttack($userBattle,$bot);
        }
        
        
        $enemyId = $this->nextTarget($team, $user->battle_id, $user->id);
        
        $enemyUserBattle = UserBattle::find()->where(['user_id' => $enemyId])->orWhere(['bot_id' => $enemyId])->one();
        

        
        
        if(!is_null($enemyUserBattle)){
            $userBattle->target = $enemyUserBattle->priority;
        }else{
            $userBattle->target = null;
        }
        
        $userBattle->save(false);
    }
    // В BattleController.php

// В BattleController.php

// Страница истории боев
// В BattleController.php

// Страница истории боев
public function actionHistory()
{
    $currentUser = Yii::$app->user->identity;
    $searchUsername = Yii::$app->request->get('username', $currentUser ? $currentUser->username : '');
    
    $user = null;
    $history = [];
    $pagination = null;
    
    if ($searchUsername) {
        $user = User::findOne(['username' => $searchUsername]);
        
        if ($user) {
            // Запрос с пагинацией
            $query = UserBattle::find()
                ->select(['battle_id', 'komand', 'total_damage'])
                ->where(['user_id' => $user->id])
                ->orderBy(['battle_id' => SORT_DESC]);
            
            $pagination = new Pagination([
                'totalCount' => $query->count(),
                'pageSize' => 20,
                'pageSizeParam' => false,
            ]);
            
            $userBattles = $query
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
            
            // Собираем данные для истории
            $historyData = [];
            foreach ($userBattles as $ub) {
                // Получаем битву
                $battle = Battle::findOne(['id' => $ub->battle_id]);
                
                // Получаем ВСЕХ участников битвы
                $allParticipants = UserBattle::find()
                    ->where(['battle_id' => $ub->battle_id])
                    ->all();
                
                $team1Players = [];
                $team2Players = [];
                $enemyIds = [];
                $enemyNames = [];
                $enemyLevels = [];
                
                foreach ($allParticipants as $participant) {
                    $playerName = '';
                    $playerLevel = '';
                    $playerId = null;
                    $isBot = false;
                    
                    if ($participant->user_id) {
                        $player = User::findOne(['id' => $participant->user_id]);
                        if ($player) {
                            $playerName = $player->username;
                            $playerLevel = $player->level;
                            $playerId = $player->id;
                            $isBot = false;
                            
                            // Собираем противников для отображения в старом формате (для совместимости)
                            if ($player->id != $user->id) {
                                $enemyIds[] = $player->id;
                                $enemyNames[] = $player->username;
                                $enemyLevels[] = $player->level;
                            }
                        }
                    } elseif ($participant->bot_id) {
                        $bot = User::findOne(['id' => $participant->bot_id]);
                        if ($bot) {
                            $playerName = $bot->username . ' (бот)';
                            $playerLevel = $bot->level;
                            $playerId = $bot->id;
                            $isBot = true;
                            
                            // Собираем противников для отображения в старом формате
                            $enemyIds[] = $bot->id;
                            $enemyNames[] = $bot->username . ' (бот)';
                            $enemyLevels[] = $bot->level;
                        }
                    }
                    
                    if ($playerName) {
                        $playerInfo = [
                            'name' => $playerName,
                            'level' => $playerLevel,
                            'id' => $playerId,
                            'isBot' => $isBot,
                        ];
                        
                        if ($participant->komand == 1) {
                            $team1Players[] = $playerInfo;
                        } else {
                            $team2Players[] = $playerInfo;
                        }
                    }
                }
                
                // Определяем победителя
                $isWinner = false;
                if ($battle && $battle->started == 2) {
                    $team1Alive = UserBattle::find()
                        ->where(['battle_id' => $ub->battle_id, 'komand' => 1, 'IsAlive' => 1])
                        ->exists();
                    $team2Alive = UserBattle::find()
                        ->where(['battle_id' => $ub->battle_id, 'komand' => 2, 'IsAlive' => 1])
                        ->exists();
                    
                    if ($team1Alive && !$team2Alive) {
                        $winnerKomand = 1;
                    } elseif (!$team1Alive && $team2Alive) {
                        $winnerKomand = 2;
                    } else {
                        $winnerKomand = null;
                    }
                    
                    $isWinner = ($winnerKomand == $ub->komand);
                }
                
                // Определяем победный знак
                $winnerSign = $isWinner ? '🏆' : '💀';
                
                // Получаем первый лог для даты
                $firstLog = BattleLog::find()
                    ->where(['battle_id' => $ub->battle_id])
                    ->orderBy(['attack_time' => SORT_ASC])
                    ->one();
                
                $historyData[] = [
                    'battle_id' => $ub->battle_id,
                    'date' => $firstLog ? date('d.m.Y H:i:s', $firstLog->attack_time) : 'Нет данных',
                    'team1_players' => $team1Players,
                    'team2_players' => $team2Players,
                    'user_komand' => $ub->komand,
                    'is_winner' => $isWinner,
                    'winner_sign' => $winnerSign,
                    'total_damage' => $ub->total_damage,
                    // Старые поля для совместимости (если нужны)
                    'enemy_names' => $enemyNames,
                    'enemy_ids' => $enemyIds,
                    'enemy_levels' => $enemyLevels,
                    'enemy_komand' => $ub->komand == 1 ? [2] : [1],
                ];
            }
            
            $history = $historyData;
        }
    }
    
    return $this->render('history', [
        'searchUsername' => $searchUsername,
        'user' => $user,
        'history' => $history,
        'pagination' => $pagination,
    ]);
}

public function actionLog($id)
{
    $battleId = $id;
    
    // Получаем параметр фильтра из GET-запроса
    $filterUsername = Yii::$app->request->get('username', '');
    
    // Получаем участников битвы
    $participants = UserBattle::find()
        ->where(['battle_id' => $battleId])
        ->all();
    
    $players = [];
    $userIds = [];
    $botIds = [];
    
    foreach ($participants as $p) {
        if ($p->user_id) {
            $user = User::findOne(['id' => $p->user_id]);
            if ($user) {
                $players[] = [
                    'id' => $user->id,
                    'name' => $user->username,
                    'type' => 'player',
                    'komand' => $p->komand,
                    'hp' => $p->hp,
                    'damage' => $p->total_damage,
                ];
                $userIds[] = $user->id;
            }
        } elseif ($p->bot_id) {
            $bot = User::findOne(['id' => $p->bot_id]);
            if ($bot) {
                $players[] = [
                    'id' => $bot->id,
                    'name' => $bot->username . ' (бот)',
                    'type' => 'bot',
                    'komand' => $p->komand,
                    'hp' => $p->hp,
                    'damage' => $p->total_damage,
                ];
                $botIds[] = $bot->id;
            }
        }
    }
    
    // Запрос логов
    $logsQuery = BattleLog::find()
        ->where(['battle_id' => $battleId]);
    
    // Применяем фильтр по нику, если он указан
    $selectedUser = null;
    if (!empty($filterUsername)) {
        // Ищем пользователя по нику (без слова (бот))
        $cleanUsername = str_replace(' (бот)', '', $filterUsername);
        $selectedUser = User::findOne(['username' => $cleanUsername]);
        
        if ($selectedUser) {
            // Фильтруем логи: показываем только где user_id = выбранный пользователь
            // или enemy_id = выбранный пользователь
            $logsQuery->andWhere([
                'or',
                ['user_id' => $selectedUser->id],
                ['enemy_id' => $selectedUser->id]
            ]);
        }
    }
    
    // Пагинация
    $pagination = new Pagination([
        'totalCount' => $logsQuery->count(),
        'pageSize' => 50,
        'pageSizeParam' => false,
    ]);
    
    $logs = $logsQuery
        ->orderBy(['attack_time' => SORT_ASC, 'id' => SORT_ASC])
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
    
    return $this->render('log', [
        'battleId' => $battleId,
        'logs' => $logs,
        'players' => $players,
        'filterUsername' => $filterUsername,
        'selectedUser' => $selectedUser,
        'pagination' => $pagination,
    ]);
}
    
}
