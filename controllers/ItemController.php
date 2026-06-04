<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Item;

class ItemController extends Controller
{
    /**
     * Выводит список предметов с иконками
     */
    public function actionIndex()
    {
        $items = Item::find()
            ->where(['not', ['img' => null]])
            ->andWhere(['<>', 'img', ''])
            ->andWhere(["complite" => 0])

            ->orderBy(['description' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        
        return $this->render('index', [
            'items' => $items,
        ]);
    }
    
    /**
     * AJAX: Устанавливает regenerated = 1 для предмета
     */
    public function actionMarkForRegeneration($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $item = Item::findOne($id);
        if (!$item) {
            return ['success' => false, 'message' => 'Предмет не найден'];
        }
        
        $item->regenerated = 1;
        if ($item->save()) {
            return ['success' => true, 'message' => 'Предмет отмечен для регенерации. Иконка будет обновлена в ближайшее время.'];
        }
        
        return ['success' => false, 'message' => 'Ошибка сохранения'];
    }
    
    /**
     * AJAX: Получение обновлённого списка предметов
     */
    public function actionGetItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $items = Item::find()
            ->select(['id', 'name', 'img', 'type', 'regenerated'])
            ->where(['not', ['img' => null]])
            ->andWhere(['<>', 'img', ''])
            ->orderBy(['description' => SORT_ASC, 'id' => SORT_ASC])
            ->asArray()
            ->all();
        
        return ['success' => true, 'items' => $items];
    }
}