<?php

namespace app\controllers;
use app\models\Item;
use app\models\Inventory;
use app\models\User;
use app\models\Elexir;
use app\models\InvetoryElexir;
use yii\data\Pagination;  // ← ДОБАВЬТЕ ЭТУ СТРОКУ
use app\models\Inventory  as InventoryModel;
use app\models\Spells;
use app\models\UserSpells;

use Yii;
class ShopController extends AppController
{
    public function actionBuy($id){
        $item = Item::findOne(["id" => $id]);
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        
        if($item->cost <=  $user->kr){
            $inventory = new Inventory();
            $inventory->user_id = $user->id;
            $inventory->item_id =$item->id;
            $inventory->name =$item->name;
            $inventory->cost =$item->cost;
            $inventory->type =$item->type; 
            $inventory->exec =$item->exec; 
            $inventory->img =$item->img; 
            $inventory->n_str =$item->n_str; 
            $inventory->n_dex =$item->n_dex; 
            $inventory->n_end =$item->n_end; 
            $inventory->n_inte =$item->n_inte; 
            $inventory->n_intu =$item->n_intu;
            $inventory->n_fire =$item->n_fire; 
            $inventory->n_water =$item->n_water; 
            $inventory->n_air =$item->n_air; 
            $inventory->n_earth =$item->n_earth; 
            $inventory->str =$item->str; 
            $inventory->dex =$item->dex; 
            $inventory->end =$item->end; 
            $inventory->inte =$item->inte; 
            $inventory->intu =$item->intu; 
            $inventory->fire =$item->fire; 
            $inventory->water =$item->water; 
            $inventory->air =$item->air; 
            $inventory->earth =$item->earth; 
            $inventory->damage =$item->damage; 
            $inventory->defence =$item->defence; 
            $inventory->health =$item->health; 
            $inventory->mana =$item->mana; 
            $inventory->n_level =$item->n_level; 
            $inventory->crit =$item->crit; 
            $inventory->anticrit =$item->anticrit; 
            $inventory->mdef =$item->mdef; 
            $inventory->evaision =$item->evaision; 
            $inventory->aeveision =$item->aeveision; 

            $inventory->save(false);
            
            $u = User::findOne(["id" => $user->id]);
            $u->kr = $u->kr - $item->cost;
            $u->save(false);
        }
        
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
    
  public function actionArt()
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        // Только артефакты (isArt = 1, isCraft = 0)
        $query = Item::find()
            ->select([
                'id', 'name', 'type', 'img', 'cost', 'cost_ekr', 'n_level',
                'n_str', 'n_dex', 'n_intu', 'n_end', 'n_inte',
                'n_water', 'n_fire', 'n_earth', 'n_air',
                'str', 'dex', 'intu', 'end', 'damage', 'defence', 
                'health', 'mana', 'crit', 'anticrit', 'mdef', 
                'evaision', 'aeveision', 'water', 'air', 'fire', 'earth'
            ])
            ->where(['not', ['img' => null]])
            ->andWhere(['<>', 'img', ''])
            ->andWhere(["isArt" => 1])
            ->andWhere(["isCraft" => 0]);

        $query->orderBy(['n_level' => SORT_ASC, 'type' => SORT_ASC, 'id' => SORT_ASC]);
        
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 24,
            'pageSizeParam' => false,
        ]);
        
        $items = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        
        // Получаем заклинание с id = 2
        $spell = Spells::findOne(['id' => 2]);
        
        return $this->render('art', [
            'items' => $items,
            'user' => $user,
            'pagination' => $pagination,
            'spell' => $spell,
        ]);
    }

public function actionBuyrepa($id){
    $item = Item::findOne(["id" => $id]);
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    // Проверка на репутацию
    if ($item->repa > $user->repa) {
        Yii::$app->session->setFlash('error', "Недостаточно репутации! Требуется {$item->repa}, у вас {$user->repa}");
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
    
    // Проверка на KR
    if($item->cost <= $user->kr){
        
        // Проверяем, не куплен ли уже этот предмет (если предметы уникальные)
        $alreadyOwned = Inventory::findOne(['user_id' => $user->id, 'item_id' => $item->id]);
        if ($alreadyOwned) {
            Yii::$app->session->setFlash('warning', "Вы уже купили этот предмет!");
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        
        $inventory = new Inventory();
        $inventory->user_id = $user->id;
        $inventory->item_id = $item->id;
        $inventory->name = $item->name;
        $inventory->cost = $item->cost;
        $inventory->type = $item->type; 
        $inventory->exec = $item->exec; 
        $inventory->img = $item->img; 
        $inventory->n_str = $item->n_str; 
        $inventory->n_dex = $item->n_dex; 
        $inventory->n_end = $item->n_end; 
        $inventory->n_inte = $item->n_inte; 
        $inventory->n_intu = $item->n_intu;
        $inventory->n_fire = $item->n_fire; 
        $inventory->n_water = $item->n_water; 
        $inventory->n_air = $item->n_air; 
        $inventory->n_earth = $item->n_earth; 
        $inventory->str = $item->str; 
        $inventory->dex = $item->dex; 
        $inventory->end = $item->end; 
        $inventory->inte = $item->inte; 
        $inventory->intu = $item->intu; 
        $inventory->fire = $item->fire; 
        $inventory->water = $item->water; 
        $inventory->air = $item->air; 
        $inventory->earth = $item->earth; 
        $inventory->damage = $item->damage; 
        $inventory->defence = $item->defence; 
        $inventory->health = $item->health; 
        $inventory->mana = $item->mana; 
        $inventory->n_level = $item->n_level; 
        $inventory->crit = $item->crit; 
        $inventory->anticrit = $item->anticrit; 
        $inventory->mdef = $item->mdef; 
        $inventory->evaision = $item->evaision; 
        $inventory->aeveision = $item->aeveision; 

        if ($inventory->save(false)) {
            $u = User::findOne(["id" => $user->id]);
            $u->kr = $u->kr - $item->cost;
            // ВАЖНО: репутация НЕ отнимается (как вы и просили)
            // $u->repa = $u->repa; - не меняем
            $u->save(false);
            
            Yii::$app->session->setFlash('success', "Вы успешно купили {$item->name}!");
        } else {
            Yii::$app->session->setFlash('error', "Ошибка при покупке предмета!");
        }
    } else {
        Yii::$app->session->setFlash('error', "Недостаточно KR! Требуется {$item->cost}, у вас {$user->kr}");
    }
    
    return $this->redirect($_SERVER['HTTP_REFERER']);
}
    
  public function actionRepa()
{
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    // Только крафтовые предметы (isCraft = 1)
    $query = Item::find()
        ->select([
            'id', 'name', 'type', 'img', 'cost', 'cost_ekr', 'n_level', 'repa',
            'n_str', 'n_dex', 'n_intu', 'n_end', 'n_inte',
            'n_water', 'n_fire', 'n_earth', 'n_air',
            'str', 'dex', 'intu', 'end', 'damage', 'defence', 
            'health', 'mana', 'crit', 'anticrit', 'mdef', 
            'evaision', 'aeveision', 'water', 'air', 'fire', 'earth'
        ])
        ->where(['not', ['img' => null]])
        ->andWhere(['<>', 'img', ''])
        ->andWhere(["isCraft" => 1]); // Оставляем только крафтовые

    // Сортировка по уровню
    $query->orderBy(['n_level' => SORT_ASC, 'type' => SORT_ASC, 'id' => SORT_ASC]);
    
    // Пагинация
    $pagination = new Pagination([
        'totalCount' => $query->count(),
        'pageSize' => 24,
        'pageSizeParam' => false,
    ]);
    
    $items = $query->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
    
    return $this->render('repa', [
        'items' => $items,
        'user' => $user,
        'pagination' => $pagination,
    ]);
}
    
   public function actionIndex()
{
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    $cat = Yii::$app->request->get('cat');
    $level = Yii::$app->request->get('level');
    $class = Yii::$app->request->get('class');
    $search = Yii::$app->request->get('search'); // ← добавить

    if ($cat == 'el') {
        $elexir = Elexir::find()->all();
        return $this->render('shop', [
            'items' => [],
            'user' => $user,
            'elexir' => $elexir,
            'selectedCat' => $cat,
            'selectedLevel' => $level,
            'selectedClass' => $class,
            'search' => $search,
        ]);
    }

    $query = Item::find()
        ->select([
            'id', 'name', 'type', 'class', 'img', 'cost', 'cost_ekr', 'n_level', 'repa', 'description',
            'n_str', 'n_dex', 'n_intu', 'n_end', 'n_inte',
            'n_water', 'n_fire', 'n_earth', 'n_air',
            'str', 'dex', 'intu', 'end', 'damage', 'defence', 
            'health', 'mana', 'crit', 'anticrit', 'mdef', 
            'evaision', 'aeveision', 'water', 'air', 'fire', 'earth'
        ])
        ->where(['not', ['img' => null]])
        ->andWhere(['<>', 'img', ''])
        ->andWhere(["isArt" => 0])
        ->andWhere(["isCraft" => 0]);

    if (!empty($cat) && $cat != 'el') {
        $query->andWhere(['type' => $cat]);
    }
    
    if (!empty($level)) {
        $query->andWhere(['n_level' => $level]);
    }
    
    if (!empty($class)) {
        $query->andWhere(['class' => $class]);
    }
    
    if (!empty($search)) {
        $query->andWhere(['like', 'description', $search]);
    }
    
    $query->orderBy(['n_level' => SORT_ASC, 'type' => SORT_ASC, 'id' => SORT_ASC]);
    
    $pagination = new Pagination([
        'totalCount' => $query->count(),
        'pageSize' => 24,
        'pageSizeParam' => false,
    ]);
    
    $items = $query->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
    
    return $this->render('shop', [
        'items' => $items,
        'user' => $user,
        'elexir' => [],
        'pagination' => $pagination,
        'selectedCat' => $cat,
        'selectedLevel' => $level,
        'selectedClass' => $class,
        'search' => $search,
    ]);
}
    
    public function actionBuyelexir($id){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);
        
        $elexir = Elexir::findOne(["id" =>$id]);
        if($elexir->cost <=  $user->kr){
            $invetoryElexir = new InvetoryElexir();
            $invetoryElexir->name = $elexir->name;
            $invetoryElexir->type = $elexir->type;
            $invetoryElexir->img = $elexir->img;
            $invetoryElexir->user_id = $user->id;
            $invetoryElexir->save(false);           
            
            $user ->kr = $user->kr - $elexir->cost;
            $user ->save(false);
        }
        
        return $this->redirect("/shop/index");
    }
    public function actionBuyekr($id){
    $item = Item::findOne(["id" => $id]);
    $user = Yii::$app->user->identity;
    $user = User::findOne(['id' => $user->id]);
    
    // Проверяем, хватает ли EKR (премиум валюты)
    if($item->cost_ekr <= $user->ekr){
        
        // Проверка, нет ли уже такого предмета в инвентаре (опционально)
        $exists = Inventory::findOne([
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
        
        if($exists) {
            Yii::$app->session->setFlash('error', 'Этот артефакт уже есть в вашем инвентаре!');
            return $this->redirect("/shop/art");
        }
        
        $inventory = new Inventory();
        $inventory->user_id = $user->id;
        $inventory->item_id = $item->id;
        $inventory->name = $item->name;
        $inventory->cost = $item->cost;
        $inventory->type = $item->type; 
        $inventory->exec = $item->exec; 
        $inventory->img = $item->img; 
        $inventory->n_str = $item->n_str; 
        $inventory->n_dex = $item->n_dex; 
        $inventory->n_end = $item->n_end; 
        $inventory->n_inte = $item->n_inte; 
        $inventory->n_intu = $item->n_intu;
        $inventory->n_fire = $item->n_fire; 
        $inventory->n_water = $item->n_water; 
        $inventory->n_air = $item->n_air; 
        $inventory->n_earth = $item->n_earth; 
        $inventory->str = $item->str; 
        $inventory->dex = $item->dex; 
        $inventory->end = $item->end; 
        $inventory->inte = $item->inte; 
        $inventory->intu = $item->intu; 
        $inventory->fire = $item->fire; 
        $inventory->water = $item->water; 
        $inventory->air = $item->air; 
        $inventory->earth = $item->earth; 
        $inventory->damage = $item->damage; 
        $inventory->defence = $item->defence; 
        $inventory->health = $item->health; 
        $inventory->mana = $item->mana; 
        $inventory->n_level = $item->n_level; 
        $inventory->crit = $item->crit; 
        $inventory->anticrit = $item->anticrit; 
        $inventory->mdef = $item->mdef; 
        $inventory->evaision = $item->evaision; 
        $inventory->aeveision = $item->aeveision; 
        $inventory->isArt = 1; 

        if($inventory->save(false)){
            // Списываем EKR
            $u = User::findOne(["id" => $user->id]);
            $u->ekr = $u->ekr - $item->cost_ekr;
            $u->save(false);
            
            Yii::$app->session->setFlash('success', 'Вы успешно купили "' . $item->name . '" за ' . number_format($item->cost_ekr, 0, ',', ' ') . ' EKR!');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при добавлении предмета в инвентарь!');
        }
    } else {
        Yii::$app->session->setFlash('error', 'Недостаточно EKR! Нужно: ' . number_format($item->cost_ekr, 0, ',', ' ') . ', у вас: ' . number_format($user->ekr, 0, ',', ' '));
    }
    
    return $this->redirect("/shop/art");
}
    
    public function actionSell($id){
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id'=>$user->id]);

        $inv = InventoryModel::findOne(['id'=>$id]);
        $cost = $inv->cost/3;
        $inv->delete();
        
        $user->kr = $user->kr + $cost;
        $user->save(false);
        return $this->redirect('/inventory/index');
    }
    
    public function actionBuySpell($id)
    {
        $user = Yii::$app->user->identity;
        $user = User::findOne(['id' => $user->id]);
        
        $spell = Spells::findOne(['id' => $id]);
        
        if (!$spell) {
            Yii::$app->session->setFlash('error', 'Заклинание не найдено!');
            return $this->redirect(['/shop/art']);
        }
        
        // Проверяем, есть ли уже такое заклинание у пользователя
        $exists = UserSpells::findOne([
            'user_id' => $user->id,
            'spell_id' => $spell->id
        ]);
        
        if ($exists) {
            Yii::$app->session->setFlash('error', 'У вас уже есть это заклинание!');
            return $this->redirect(['/shop/art']);
        }
        
        // Проверяем достаточно ли EKR
        if ($user->ekr < 1) {
            Yii::$app->session->setFlash('error', 'Недостаточно EKR! Нужно: 1 EKR');
            return $this->redirect(['/shop/art']);
        }
        
        // Списываем EKR
        $user->ekr -= 1;
        $user->save(false);
        
        // Добавляем заклинание пользователю
        $userSpell = new UserSpells();
        $userSpell->user_id = $user->id;
        $userSpell->spell_id = $spell->id;
        $userSpell->save(false);
        
        Yii::$app->session->setFlash('success', 'Вы купили заклинание "' . $spell->name . '" за 1 EKR!');
        
        return $this->redirect(['/shop/art']);
    }
}
