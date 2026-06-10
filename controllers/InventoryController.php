<?php

namespace app\controllers;
use app\models\User;
use app\models\Inventory;
use app\models\Item;
use app\models\InvetoryElexir;
use app\models\UserElexir;
use app\models\UserLevels;
use app\models\UserSpells;
use app\models\Spells;

use Yii;

class InventoryController extends AppController
{
    
    public function actionUseelexir($id){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        
        $inventoryElexir = InvetoryElexir::findOne(["id" =>$id,"user_id" =>$user->id]);
        
        $userElexir = new UserElexir();
        $userElexir->name = $inventoryElexir->name;
        $userElexir->img  = $inventoryElexir->img;
        $userElexir->type  = $inventoryElexir->type;
        $userElexir->user_id  = $user->id;
        $userElexir->use_time  = time()+60*60*2;
        $userElexir->save(false);
        
        $inventoryElexir->delete();
        
        if($inventoryElexir->type == 'str'){
            $user->str = $user->str + 10;
        }
        if($inventoryElexir->type == 'dex'){
            $user->dex = $user->dex + 10;
        }
        if($inventoryElexir->type == 'intu'){
            $user->intu = $user->intu + 10;
        }
        if($inventoryElexir->type == 'inte'){
            $user->inte = $user->inte + 10;
        }
        $user->save(false);
        return $this->redirect("/inventory/index");
    }
    
    
public function actionIndex()
{
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    // Получаем следующий UP для расчёта опыта
    $nextLevelUp = null;
    $currentLevelUp = null;
    
    // Текущий UP пользователя
    $currentLevelUp = UserLevels::findOne([
        'level' => $user->level,
        'up' => $user->up
    ]);
    
    // Следующий UP
    $nextLevelUp = UserLevels::findOne([
        'level' => $user->level,
        'up' => $user->up + 1
    ]);
    
    // Если следующего UP нет в текущем уровне - переходим на следующий уровень
    if (!$nextLevelUp) {
        $nextLevelUp = UserLevels::findOne([
            'level' => $user->level + 1,
            'up' => 1
        ]);
    }
    
    $elexir = InvetoryElexir::findAll(["user_id" => $user->id]);
    $inventory = Inventory::findAll(["user_id" => $user->id, "dressed" => 0, "mailed" => 0,"shoped" => 0]);
    $userElexir = UserElexir::findAll(["user_id" => $user->id]);
    
    // ПОЛУЧАЕМ ВСЕ ЗАКЛИНАНИЯ ПОЛЬЗОВАТЕЛЯ
    $userSpells = [];
    $userSpellsRaw = UserSpells::findAll(["user_id" => $user->id]);
    
    foreach ($userSpellsRaw as $us) {
        $spell = Spells::findOne(["id" => $us->spell_id]);
        if ($spell) {
            $userSpells[] = $spell;
        }
    }
    
    return $this->render('index', [
        "items" => $inventory,
        "user" => $user,
        "elexir" => $elexir,
        "userElexir" => $userElexir,
        "currentLevelUp" => $currentLevelUp,
        "nextLevelUp" => $nextLevelUp,
        "spells" => $userSpells  // Передаём заклинания в представление
    ]);
}
    
    public function actionDress($id){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);

        $inv = Inventory::findOne(['id'=>$id]);
        
        if(!is_null($inv)){
            $item = $inv;
            if(!is_null($item)){
               $type = $item->type;
                   if($type != 'ring'){
                    $this->undress($type,$user,$user->{$type});
                   }
                   if($this->checkRequirements($item)){
                     $this->dress($inv,$item,$type);
                     return $this->redirect("/inventory/index");
                   }else{
                       return $this->redirect("/inventory/index");
                   }
               
            }else{
                echo 'iterm null';
            }
        }else{
                echo 'inv nbull';
        }
    }
    
    public function dress($inv,$item,$type){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        
        $inv->dressed = 1;
        $inv->save();
        
        if($type != 'ring'){
            $user->{$type} = $item->id;
        }else{
            if(is_null($user->ring1)){
                $user->ring1 = $item->id;
            } elseif(is_null($user->ring2)){
                $user->ring2 = $item->id;
            }elseif(is_null($user->ring3)){
                $user->ring3 = $item->id;
            }else{
                return;
            }
        }
        
        $user->str += $item->str;
        $user->dex += $item->dex;
        $user->intu += $item->intu;
        $user->inte += $item->inte;
        $user->endu += $item->end;
        $user->fire += $item->fire;
        $user->water += $item->water;
        $user->air += $item->air;
        $user->earth += $item->earth;
        $user->damage += $item->damage;
        $user->defence += $item->defence;
        $user->health += $item->health;
        $user->mana += $item->mana;
        $user->crit += $item->crit;
        $user->anticrit += $item->anticrit;
        $user->mdef += $item->mdef;
        $user->evaision += $item->evaision;
        $user->aeveision += $item->aeveision;
        $user->save(false);
        
    }
    
    public function actionPlus($type){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        if($user->points > 0){
            $user->{$type} += 1;
            $user->points -= 1;
            $user->save(false);
            return $this->redirect("/inventory/index");
        }
    }
    
    public function actionUndress($type){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        if($user->{$type} != null){
            $id = $user->{$type};
            $item = Inventory::findOne(["id"=>$id]);
            

            $this->undress($type, $user, $item->id);
            $user->{$type} = null;
            $user->save(false);

        }
        return $this->redirect("/inventory/index");
    }
    
    public function undress($type,$user,$id){
        $user = User::findOne(["id" => $user->id]);
        
        if($type == 'ring'){
            return;
        }
        
        if (!is_null($user->{$type})) {
            $item = Inventory::findOne(['id' => $id]);
            
            $user->str -= $item->str;
            $user->dex -= $item->dex;
            $user->intu -= $item->intu;
            $user->inte -= $item->inte;
            $user->endu -= $item->end;
            $user->fire -= $item->fire;
            $user->water -= $item->water;
            $user->air -= $item->air;
            $user->earth -= $item->earth;
            $user->damage -= $item->damage;
            $user->defence -= $item->defence;
            $user->health -= $item->health;
            $user->mana -= $item->mana;
            $user->crit -= $item->crit;
            $user->anticrit -= $item->anticrit;
            $user->mdef -= $item->mdef;
            $user->evaision -= $item->evaision;
            $user->aeveision -= $item->aeveision;
            
            $user->save(false);
            
            $item->dressed = 0;
            $item->save(false);
        }
    }
    
    public function checkRequirements($item){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        

            
        if($user->str < $item->n_str){
            return false;
        }
        if($user->dex < $item->n_dex){
            return false;
        }
        if($user->inte < $item->n_inte){
            return false;
        }
        if($user->intu < $item->n_intu){
            return false;
        }
        if($user->endu < $item->n_end){
            return false;
        }
        if($user->fire < $item->n_fire){
            return false;
        }
        if($user->water < $item->n_water){
            return false;
        }
        if($user->earth < $item->n_earth){
            return false;
        }
        if($user->air < $item->n_air){
            return false;
        }
        if($user->level < $item->n_level){
            return false;
        }
        return true;
    }

    public function actionAddstats()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    $stat = Yii::$app->request->post('stat');
    $amount = (int)Yii::$app->request->post('amount');
    
    $allowedStats = ['str', 'dex', 'intu', 'endu', 'inte', 'earth', 'fire', 'water', 'air'];
    
    if (!in_array($stat, $allowedStats)) {
        return ['success' => false, 'error' => 'Недопустимый параметр'];
    }
    
    if ($amount <= 0) {
        return ['success' => false, 'error' => 'Количество должно быть больше 0'];
    }
    
    if ($user->points < $amount) {
        return ['success' => false, 'error' => "Недостаточно очков. У вас {$user->points} очков, запрошено {$amount}"];
    }
    
    // Добавляем статы
    $user->{$stat} += $amount;
    $user->points -= $amount;
    $user->save(false);
    
    return [
        'success' => true,
        'newValue' => $user->{$stat},
        'remainingPoints' => $user->points,
        'statName' => $stat
    ];
}

public function actionRemoveexpiredelixirs()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    // Находим все истекшие эликсиры
    $expiredElixirs = UserElexir::find()
        ->where(['user_id' => $user->id])
        ->andWhere(['<', 'use_time', time()])
        ->all();
    
    $removedCount = 0;
    
    foreach ($expiredElixirs as $elixir) {
        // Снимаем бонусы с пользователя
        if ($elixir->type == 'str') {
            $user->str -= 10;
        }
        if ($elixir->type == 'dex') {
            $user->dex -= 10;
        }
        if ($elixir->type == 'intu') {
            $user->intu -= 10;
        }
        if ($elixir->type == 'inte') {
            $user->inte -= 10;
        }
        
        // Удаляем эликсир
        $elixir->delete();
        $removedCount++;
    }
    
    if ($removedCount > 0) {
        $user->save(false);
    }
    
    return [
        'success' => true,
        'removed_count' => $removedCount
    ];
}


public function actionUndressring($slot)
{
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    // Разрешенные слоты для колец
    $allowedSlots = ['ring1', 'ring2', 'ring3'];
    
    if (!in_array($slot, $allowedSlots)) {
        return $this->redirect("/inventory/index");
    }
    
    if ($user->{$slot} != null) {
        $id = $user->{$slot};
        $item = Inventory::findOne(["id" => $id]);
        
        if ($item) {
            // Снимаем бонусы
            $user->str -= $item->str;
            $user->dex -= $item->dex;
            $user->intu -= $item->intu;
            $user->inte -= $item->inte;
            $user->endu -= $item->end;
            $user->fire -= $item->fire;
            $user->water -= $item->water;
            $user->air -= $item->air;
            $user->earth -= $item->earth;
            $user->damage -= $item->damage;
            $user->defence -= $item->defence;
            $user->health -= $item->health;
            $user->mana -= $item->mana;
            $user->crit -= $item->crit;
            $user->anticrit -= $item->anticrit;
            $user->mdef -= $item->mdef;
            $user->evaision -= $item->evaision;
            $user->aeveision -= $item->aeveision;
            
            // Очищаем слот
            $user->{$slot} = null;
            $user->save(false);
            
            // Помечаем предмет как снятый
            $item->dressed = 0;
            $item->save(false);
        }
    }
    
    return $this->redirect("/inventory/index");
}    
}
