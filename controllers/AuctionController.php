<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\Pagination;
use app\models\Auction;
use app\models\Inventory;
use app\models\User;
use app\models\Item;

class AuctionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    // Главная страница аукциона - просмотр лотов
public function actionIndex()
{
    $userId = Yii::$app->user->id;
    $user = User::findOne($userId);
    
    $query = Auction::find()
        ->with(['user', 'inventory'])
        ->orderBy(['create_time' => SORT_DESC]);
    
    // Фильтр по типу предмета
    $typeFilter = Yii::$app->request->get('type');
    if ($typeFilter && $typeFilter !== 'all') {
        $query->joinWith('inventory')->andWhere(['inventory.type' => $typeFilter]);
    }
    
    // Фильтр по уровню
    $levelFilter = Yii::$app->request->get('level');
    if ($levelFilter && $levelFilter !== 'all') {
        $query->joinWith('inventory')->andWhere(['inventory.n_level' => $levelFilter]);
    }
    
    $pagination = new Pagination([
        'totalCount' => $query->count(),
        'pageSize' => 12,
    ]);
    
    $lots = $query->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
    
    // Получаем типы предметов для фильтра
    $types = Item::find()->select(['type'])->distinct()->where(['is', 'type', null])->orWhere(['!=', 'type', ''])->column();
    
    // Уровни для фильтра
    $levels = range(1, 20);
    
    // Предметы для продажи (не выставленные)
    $sellItems = Inventory::find()
        ->where(['user_id' => $userId, 'dressed' => 0, 'shoped' => 0, 'mailed' => 0])
        ->all();
    
    // Мои лоты (выставленные на продажу)
    $myLots = Inventory::find()
        ->where(['user_id' => $userId, 'shoped' => 1])
        ->with('auction')
        ->orderBy(['id' => SORT_DESC])
        ->all();
    
    
    
    return $this->render('index', [
        'user' => $user,
        'lots' => $lots,
        'pagination' => $pagination,
        'types' => $types,
        'typeFilter' => $typeFilter,
        'levelFilter' => $levelFilter,
        'levels' => $levels,
        'sellItems' => $sellItems,
        'myLots' => $myLots,
    ]);
}

    public function actionBuy($id)
{
    $userId = Yii::$app->user->id;
    $user = User::findOne($userId);
    
    $lot = Auction::findOne($id);
    if (!$lot) {
        Yii::$app->session->setFlash('error', 'Лот не найден!');
        return $this->redirect(['index', 'tab' => 'buy']);
    }
    
    $item = Inventory::findOne($lot->item_id);
    if (!$item) {
        Yii::$app->session->setFlash('error', 'Предмет не найден!');
        return $this->redirect(['index', 'tab' => 'buy']);
    }
    
    // Проверяем достаточно ли денег
    if ($user->kr < $lot->cost) {
        Yii::$app->session->setFlash('error', 'Недостаточно KR! Нужно: ' . number_format($lot->cost, 0, ',', ' '));
        return $this->redirect(['index', 'tab' => 'buy']);
    }
    
    $transaction = Yii::$app->db->beginTransaction();
    
    try {
        // Списываем деньги у покупателя
        $user->kr -= $lot->cost;
        $user->save(false);
        
        // Добавляем деньги продавцу
        $seller = User::findOne($lot->user_id);
        if ($seller) {
            $seller->kr += $lot->cost;
            $seller->save(false);
        }
        
        // Передаем предмет покупателю
        $item->user_id = $userId;
        $item->shoped = 0;
        $item->save(false);
        
        // Удаляем лот из аукциона
        $lot->delete();
        
        $transaction->commit();
        
        Yii::$app->session->setFlash('success', 'Предмет "' . $item->name . '" успешно куплен!');
    } catch (\Exception $e) {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', 'Ошибка при покупке!');
    }
    
    return $this->redirect(['index', 'tab' => 'buy']);
}

    // Мои заявки (выставленные на продажу)
    public function actionMyLots()
    {
        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);
        
        $query = Inventory::find()
            ->where(['user_id' => $userId, 'shoped' => 1])
            ->orderBy(['id' => SORT_DESC]);
        
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 12,
        ]);
        
        $items = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        
        return $this->render('my-lots', [
            'user' => $user,
            'items' => $items,
            'pagination' => $pagination,
        ]);
    }

    // Снять предмет с продажи
public function actionRemoveLot($id)
{
    $userId = Yii::$app->user->id;
    
    $item = Inventory::findOne($id);
    if (!$item) {
        Yii::$app->session->setFlash('error', 'Предмет не найден!');
        return $this->redirect(['index']);
    }
    
    if ($item->user_id != $userId) {
        Yii::$app->session->setFlash('error', 'Это не ваш предмет!');
        return $this->redirect(['index']);
    }
    
    // Удаляем лот из аукциона
    Auction::deleteAll(['item_id' => $id]);
    
    // Снимаем с продажи
    $item->shoped = 0;
    $item->save(false);
    
    Yii::$app->session->setFlash('success', 'Предмет снят с продажи!');
    
    // РЕДИРЕКТ НА ГЛАВНУЮ АУКЦИОНА (ВКЛАДКА КУПИТЬ)
    return $this->redirect(['index', 'tab' => 'buy']);
}

    // Продать предмет
    // Продать предмет
public function actionSell()
{
    $userId = Yii::$app->user->id;
    $user = User::findOne($userId);
    
    // Получаем предметы, которые можно продать
    $items = Inventory::find()
        ->where(['user_id' => $userId, 'dressed' => 0, 'shoped' => 0, 'mailed' => 0])
        ->all();
    
    if (Yii::$app->request->isPost) {
        $itemId = Yii::$app->request->post('item_id');
        $price = Yii::$app->request->post('price');
        
        if (!$itemId) {
            Yii::$app->session->setFlash('error', 'Выберите предмет!');
            return $this->redirect(['sell']);
        }
        
        if (!$price || $price <= 0) {
            Yii::$app->session->setFlash('error', 'Введите корректную цену!');
            return $this->redirect(['sell']);
        }
        
        $item = Inventory::findOne($itemId);
        if (!$item || $item->user_id != $userId) {
            Yii::$app->session->setFlash('error', 'Предмет не найден!');
            return $this->redirect(['sell']);
        }
        
        if ($item->dressed == 1 || $item->shoped == 1 || $item->mailed == 1) {
            Yii::$app->session->setFlash('error', 'Этот предмет нельзя продать!');
            return $this->redirect(['sell']);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Добавляем в аукцион
            $auction = new Auction();
            $auction->user_id = $userId;
            $auction->item_id = $item->id;
            $auction->cost = $price;
            $auction->create_time = time();
            $auction->save();
            
            // Помечаем предмет как выставленный на продажу
            $item->shoped = 1;
            $item->save(false);
            
            $transaction->commit();
            
            Yii::$app->session->setFlash('success', 'Предмет "' . $item->name . '" выставлен на продажу за ' . number_format($price, 0, ',', ' ') . ' KR!');
            
            // РЕДИРЕКТ НА ГЛАВНУЮ АУКЦИОНА, А НЕ НА SELL
            return $this->redirect(['index']);
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Ошибка при выставлении на продажу!');
            return $this->redirect(['sell']);
        }
    }
    
    return $this->render('sell', [
        'user' => $user,
        'items' => $items,
    ]);
}
}