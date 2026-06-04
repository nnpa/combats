<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Battle;
use app\models\UserBattle;
use app\models\BattleLog;
use app\models\Auction;  // <-- ДОБАВЬТЕ ЭТУ СТРОКУ
use app\models\Inventory;  // <-- ДОБАВЬТЕ ЭТУ СТРОКУ
use app\models\Mailed;  // <-- ДОБАВЬТЕ ЭТУ СТРОКУ

class CleanController extends Controller
{
    /**
     * Очистка старых битв (старше 7 дней)
     * Использование: php yii clean/old-battles
     */
    public function actionOldBattles()
    {
        $days = 7;
        $timestamp = time() - ($days * 24 * 60 * 60);
        
        echo "[" . date('Y-m-d H:i:s') . "] Начинаю очистку битв старше {$days} дней (до " . date('Y-m-d H:i:s', $timestamp) . ")\n";
        
        // Находим старые завершенные битвы (started = 2)
        $oldBattles = Battle::find()
            ->where(['started' => 2])
            ->andWhere(['<', 'start_time', $timestamp])
            ->all();
        
        if (empty($oldBattles)) {
            echo "[" . date('Y-m-d H:i:s') . "] Нет старых битв для очистки\n";
            return;
        }
        
        $battleIds = [];
        foreach ($oldBattles as $battle) {
            $battleIds[] = $battle->id;
        }
        
        $count = count($battleIds);
        echo "[" . date('Y-m-d H:i:s') . "] Найдено {$count} старых битв\n";
        
        // Удаляем логи битв
        $deletedLogs = BattleLog::deleteAll(['battle_id' => $battleIds]);
        echo "[" . date('Y-m-d H:i:s') . "] Удалено {$deletedLogs} записей из battle_log\n";
        
        // Удаляем участников битв
        $deletedParticipants = UserBattle::deleteAll(['battle_id' => $battleIds]);
        echo "[" . date('Y-m-d H:i:s') . "] Удалено {$deletedParticipants} записей из user_battle\n";
        
        // Удаляем сами битвы
        $deletedBattles = Battle::deleteAll(['id' => $battleIds]);
        echo "[" . date('Y-m-d H:i:s') . "] Удалено {$deletedBattles} записей из battle\n";
        
        echo "[" . date('Y-m-d H:i:s') . "] Очистка завершена!\n";
    }
    
    /**
     * Очистка неактивных ивентов (старше 1 дня)
     * Использование: php yii clean/old-events
     */
    public function actionOldEvents()
    {
        $timestamp = time() - (24 * 60 * 60);
        $date = date('Y-m-d H:i:s', $timestamp);
        
        echo "[" . date('Y-m-d H:i:s') . "] Начинаю очистку ивентов до {$date}\n";
        
        // Находим старые неактивные ивенты
        $oldEvents = \app\models\EventBoss::find()
            ->where(['is_active' => 0])
            ->andWhere(['<', 'end_time', $date])
            ->all();
        
        if (empty($oldEvents)) {
            echo "[" . date('Y-m-d H:i:s') . "] Нет старых ивентов для очистки\n";
            return;
        }
        
        $eventIds = [];
        foreach ($oldEvents as $event) {
            $eventIds[] = $event->id;
        }
        
        $count = count($eventIds);
        echo "[" . date('Y-m-d H:i:s') . "] Найдено {$count} старых ивентов\n";
        
        // Удаляем участников ивентов
        $deletedParticipants = \app\models\EventParticipants::deleteAll(['event_id' => $eventIds]);
        echo "[" . date('Y-m-d H:i:s') . "] Удалено {$deletedParticipants} записей из event_participants\n";
        
        // Удаляем сами ивенты
        $deletedEvents = \app\models\EventBoss::deleteAll(['id' => $eventIds]);
        echo "[" . date('Y-m-d H:i:s') . "] Удалено {$deletedEvents} записей из event_boss\n";
        
        echo "[" . date('Y-m-d H:i:s') . "] Очистка ивентов завершена!\n";
    }
    
/**
 * Очистка старых лотов аукциона (старше 7 дней)
 * Использование: php yii clean/old-auction
 */
public function actionOldAuction($days = 7)
{
    $timestamp = time() - ($days * 24 * 60 * 60);
    
    echo "[" . date('Y-m-d H:i:s') . "] Начинаю очистку аукциона (лоты старше {$days} дней)\n";
    
    $oldLots = Auction::find()
        ->where(['<', 'create_time', $timestamp])
        ->all();
    
    if (empty($oldLots)) {
        echo "[" . date('Y-m-d H:i:s') . "] Нет старых лотов для очистки\n";
        return;
    }
    
    $count = count($oldLots);
    echo "[" . date('Y-m-d H:i:s') . "] Найдено {$count} старых лотов\n";
    
    $processed = 0;
    $returnedItems = 0;
    $notFound = 0;
    
    foreach ($oldLots as $lot) {
        echo "  Обработка лота ID={$lot->id}: user_id={$lot->user_id}, item_id={$lot->item_id}\n";
        
        // Ищем предмет в инвентаре по id (item_id в auction - это id из inventory)
        $inventory = Inventory::find()
            ->where(['id' => $lot->item_id, 'user_id' => $lot->user_id])
            ->one();
        
        if ($inventory) {
            // Возвращаем предмет владельцу (shoped = 0)
            $inventory->shoped = 0;
            $inventory->save(false);
            $returnedItems++;
            echo "    ✓ Возвращен предмет ID={$inventory->id} (shoped=0)\n";
        } else {
            $notFound++;
            echo "    ✗ Предмет не найден в инвентаре\n";
        }
        
        $lot->delete();
        $processed++;
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Обработано {$processed} лотов\n";
    echo "[" . date('Y-m-d H:i:s') . "] Возвращено {$returnedItems} предметов в инвентарь\n";
    echo "[" . date('Y-m-d H:i:s') . "] Не найдено: {$notFound} предметов\n";
    echo "[" . date('Y-m-d H:i:s') . "] Очистка аукциона завершена!\n";
}

/**
     * Полная очистка (битвы + ивенты + аукцион + почта)
     * Использование: php yii clean/all
     */
    public function actionAll()
    {
        echo "[" . date('Y-m-d H:i:s') . "] === НАЧАЛО ПОЛНОЙ ОЧИСТКИ ===\n";
        
        echo "\n--- ОЧИСТКА БИТВ ---\n";
        $this->actionOldBattles();
        
        echo "\n--- ОЧИСТКА ИВЕНТОВ ---\n";
        $this->actionOldEvents();
        
        echo "\n--- ОЧИСТКА АУКЦИОНА ---\n";
        $this->actionOldAuction();
        
        echo "\n--- ОЧИСТКА ПОЧТЫ ---\n";
        $this->actionOldMail();
        
        echo "\n[" . date('Y-m-d H:i:s') . "] === ПОЛНАЯ ОЧИСТКА ЗАВЕРШЕНА ===\n";
    }
    
/**
 * Очистка старых писем почты (старше N дней)
 * Использование: php yii clean/old-mail
 */
public function actionOldMail($days = 1)
{
    $timestamp = time() - ($days * 24 * 60 * 60);
    
    echo "[" . date('Y-m-d H:i:s') . "] Начинаю очистку почты (письма старше {$days} дней)\n";
    echo "  Порог времени: " . date('Y-m-d H:i:s', $timestamp) . "\n";
    
    // Находим старые письма (не завершенные)
    $oldMails = Mailed::find()
        ->where(['<', 'created_time', $timestamp])
        ->andWhere(['complited' => 0])
        ->all();
    
    if (empty($oldMails)) {
        echo "[" . date('Y-m-d H:i:s') . "] Нет старых писем для очистки\n";
        return;
    }
    
    $count = count($oldMails);
    echo "[" . date('Y-m-d H:i:s') . "] Найдено {$count} старых писем\n";
    
    $processed = 0;
    $returnedItems = 0;
    
    foreach ($oldMails as $mail) {
        echo "\n  === Письмо ID: {$mail->id} ===";
        echo "\n    from_user_id: {$mail->from_user_id}";
        echo "\n    to_user_id: {$mail->to_user_id}";
        echo "\n    item_id: {$mail->item_id}";
        echo "\n    cost: {$mail->cost}";
        echo "\n    created_time: " . date('Y-m-d H:i:s', $mail->created_time) . "\n";
        
        // Ищем предмет в инвентаре
        $item = Inventory::findOne($mail->item_id);
        
        if ($item) {
            // Возвращаем предмет отправителю (как в actionReject)
            $item->user_id = $mail->from_user_id;
            $item->mailed = 0;
            if ($item->save(false)) {
                $returnedItems++;
                echo "    ✓ Предмет ID={$item->id} возвращен отправителю {$mail->from_user_id}\n";
            } else {
                echo "    ✗ Ошибка при возврате предмета\n";
            }
        } else {
            echo "    ✗ Предмет с ID={$mail->item_id} не найден в инвентаре\n";
        }
        
        // Удаляем письмо
        if ($mail->delete()) {
            echo "    ✓ Письмо удалено\n";
        } else {
            echo "    ✗ Ошибка при удалении письма\n";
        }
        $processed++;
    }
    
    echo "\n[" . date('Y-m-d H:i:s') . "] Обработано {$processed} писем\n";
    echo "[" . date('Y-m-d H:i:s') . "] Возвращено {$returnedItems} предметов отправителям\n";
    echo "[" . date('Y-m-d H:i:s') . "] Очистка почты завершена!\n";
}
    
}