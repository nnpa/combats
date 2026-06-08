<?php
// Подключаем Yii, чтобы получить доступ к базе данных
require __DIR__ . '/web/index.php';

use app\models\Item;
use yii\helpers\FileHelper;

// Все уникальные пары (класс, уровень)
$items = Item::find()
    ->select(['class', 'n_level'])
    ->where(['is', 'class', null])->orWhere(['!=', 'class', '']) // исключаем NULL и пустые строки
    ->distinct()
    ->all();

$basePath = Yii::getAlias('@webroot') . '/generated_items/';

foreach ($items as $item) {
    if (empty($item->class) || empty($item->n_level)) continue;

    // Формируем имя папки: например "Критовик_2ур"
    $folderName = $item->class . '_' . $item->n_level . 'ур';
    $fullPath = $basePath . $folderName;

    if (!is_dir($fullPath)) {
        FileHelper::createDirectory($fullPath, 0777);
        echo "✅ Создана папка: {$folderName}\n";
    } else {
        echo "⚠️ Папка уже существует: {$folderName}\n";
    }
}

echo "\n🎉 Готово! Все папки созданы.\n";