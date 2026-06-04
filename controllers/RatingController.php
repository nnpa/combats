<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\User;
use app\models\Clan;
use app\models\ClanUser;

class RatingController extends Controller
{
public function actionIndex()
{
    // Передаем начальные значения для сортировки
    $sort = Yii::$app->request->get('sort', 'exp');
    $order = Yii::$app->request->get('order', 'desc');
    $clanSort = Yii::$app->request->get('clan_sort', 'total_exp');
    $clanOrder = Yii::$app->request->get('clan_order', 'desc');
    
    return $this->render('index', [
        'sort' => $sort,
        'order' => $order,
        'clanSort' => $clanSort,
        'clanOrder' => $clanOrder,
    ]);
}
    
    // Рейтинг игроков
// Рейтинг игроков
public function actionPlayers()
{
    $sort = Yii::$app->request->get('sort', 'exp');
    $order = Yii::$app->request->get('order', 'desc');
    $page = Yii::$app->request->get('page', 1);
    
    $allowedSort = ['exp', 'repa', 'win', 'level', 'kr', 'username'];
    if (!in_array($sort, $allowedSort)) {
        $sort = 'exp';
    }
    
    $orderDir = ($order === 'asc') ? SORT_ASC : SORT_DESC;
    
    $query = User::find()
        ->where(['bot' => 0])
        ->andWhere(['status' => 10]);
    
    $countQuery = clone $query;
    $pagination = new Pagination([
        'totalCount' => $countQuery->count(),
        'pageSize' => 20,
        'page' => $page - 1,
        'pageSizeParam' => false,
    ]);
    
    $players = $query
        ->orderBy([$sort => $orderDir])
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
    
    $rank = $pagination->offset + 1;
    
    return $this->renderPartial('_players', [
        'players' => $players,
        'pagination' => $pagination,
        'sort' => $sort,      // ← ДОБАВЬ ЭТУ СТРОКУ
        'order' => $order,    // ← ДОБАВЬ ЭТУ СТРОКУ
        'rank' => $rank,
    ]);
}
// Рейтинг кланов
// Рейтинг кланов
public function actionClans()
{
    $sort = Yii::$app->request->get('sort', 'total_exp');
    $order = Yii::$app->request->get('order', 'desc');
    $page = Yii::$app->request->get('page', 1);
    
    $allowedSort = ['total_exp', 'total_repa', 'total_win', 'members_count', 'name'];
    if (!in_array($sort, $allowedSort)) {
        $sort = 'total_exp';
    }
    
    $orderDir = ($order === 'asc') ? 'ASC' : 'DESC';
    
    // Получаем все кланы с подсчетом статистики
    $query = Clan::find()
        ->select([
            'clan.*',
            'COALESCE(SUM(u.exp), 0) as total_exp',
            'COALESCE(SUM(u.repa), 0) as total_repa',
            'COALESCE(SUM(u.win), 0) as total_win',
            'COUNT(cu.user_id) as members_count'
        ])
        ->leftJoin('clan_user cu', 'cu.clan_id = clan.id AND cu.status = 1')
        ->leftJoin('user u', 'u.id = cu.user_id AND u.bot = 0')
        ->groupBy('clan.id')
        ->orderBy("$sort $orderDir");
    
    $totalCount = Clan::find()->count();
    
    $pagination = new Pagination([
        'totalCount' => $totalCount,
        'pageSize' => 20,
        'page' => $page - 1,
        'pageSizeParam' => false,
    ]);
    
    $clans = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
    
    $rank = $pagination->offset + 1;
    
    return $this->renderPartial('_clans', [
        'clans' => $clans,
        'pagination' => $pagination,
        'sort' => $sort,
        'order' => $order,
        'rank' => $rank,
    ]);
}
}