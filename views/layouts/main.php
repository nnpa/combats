<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\models\User;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <style>
        /* Средневековый стиль для меню */
        .medieval-navbar {
            background: #1a0f0a !important;
            border-bottom: 3px solid #8b5a2b;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            font-family: 'Cinema', 'MedievalSharp', 'Courier New', monospace;
        }
        .medieval-navbar .navbar-brand {
            font-size: 1.8rem;
            letter-spacing: 3px;
            text-shadow: 2px 2px 0 #4a2a0a;
            color: #e6c87e !important;
        }
        .medieval-navbar .nav-link {
            color: #d4b87a !important;
            font-size: 1.1rem;
            transition: all 0.3s;
            text-shadow: 1px 1px 0 #2a1a0a;
            margin: 0 5px;
        }
        .medieval-navbar .nav-link:hover {
            color: #f5e6b0 !important;
            text-shadow: 0 0 5px #f5a623;
            transform: scale(1.05);
        }
        .medieval-navbar .logout {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        .medieval-navbar .navbar-toggler {
            border-color: #8b5a2b;
        }
    </style>
    <?php
    NavBar::begin([
        'brandLabel' => '🏰 Dungeon RPG  ⚔️',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md medieval-navbar fixed-top']
    ]);

    $menuItems = [];

    // Общие пункты для всех

    // Пункты для авторизованных пользователей
    if (!Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Базар', 'url' => ['/auction/index']];
        $menuItems[] = ['label' => '🏛️ На площадь', 'url' => ['/site/cp']];
        $menuItems[] = ['label' => '🎒 Инвентарь', 'url' => ['/inventory/index']];
        $menuItems[] = [
            'label' => 'Сообщество',
            'items' => [
                ['label' => '🏆 ТОП игроков', 'url' => ['/rating/index']],
                ['label' => '💬 Форум', 'url' => ['/forum/index']],
                ['label' => '⚙️ Настройки', 'url' => ['/settings/index']],
            ]
        ];

    }

    // Пункты для неавторизованных
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '📜 Регистрация', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '🔑 Вход', 'url' => ['/site/login']];
    } else {
        // Кнопка выхода для авторизованных
        $menuItems[] = '<li class="nav-item">'
                    . Html::beginForm(['/site/logout'])
                    . Html::submitButton(
                        '🚪 Выход (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'nav-link btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => $menuItems
    ]);

    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>



<?php $this->endBody() ?>
    <?php 
        $user = Yii::$app->user->identity;
        if(!is_null($user)):
            
            $user = User::findOne(['id'=>$user->id]); ?>
            <?php if(!is_null($user->battle_id) && is_null($user->in_battle)):?>
                <div id="weblog"></div>
                <script>
                    const weblog = document.getElementById('weblog');

                    function addMessage(text, type = 'system') {
                        //const div = document.createElement('div');
                      // div.className = `msg ${type}`;
                       // div.textContent = text;
                      //  weblog.appendChild(div);
                      //  weblog.scrollTop = log.scrollHeight;
                    }

                    // Получаем userId из PHP
                    const userId = '<?php echo $user->session_id;?>';

                    function connect() {
                        const ws = new WebSocket(`ws://localhost:8080/ws?user=${userId}`);

                        ws.onopen = () => {
                            addMessage(`✅ Соединение установлено. Ваш ID: ${userId}`, 'system');
                        };

                        ws.onmessage = (event) => {
                            const data = JSON.parse(event.data); // Парсим JSON

                            switch(data.type) {
                                case 'command':
                                    if (data.command === 'reload') {
                                        // Немедленная перезагрузка без задержки
                                        addMessage('🔄 Перезагрузка страницы...', 'reload');
                                        location.reload(true); // Принудительная перезагрузка
                                    }
                                    break;

                                default:
                                    addMessage(`📩 От сервера: ${JSON.stringify(data)}`, 'server');
                            }
                        };

                        ws.onclose = () => {
                            addMessage('❌ Соединение закрыто. Пытаемся переподключиться...', 'system');
                            setTimeout(connect, 3000); // Автоматический реконнект
                        };

                        ws.onerror = () => {
                            addMessage('⚠ Ошибка соединения.', 'system');
                        };
                    }

                    // Запускаем соединение при загрузке страницы
                    connect();
                </script>
            <?php 
        endif; ?>
        <?php
            //echo \app\widgets\ChatWidget::widget();
        ?>
<?php endif; ?>
                <style>
/* Отступ для контента под чат */
body {
    padding-bottom: 350px !important;
}

@media (max-height: 700px) {
    body {
        padding-bottom: 300px !important;
    }
}

@media (max-height: 600px) {
    body {
        padding-bottom: 250px !important;
    }
}

@media (max-height: 500px) {
    body {
        padding-bottom: 200px !important;
    }
}
</style>
<?php 
$user = Yii::$app->user->identity; if(!is_null($user)): ?>
            
        
<?= \app\widgets\ChatWidget::widget() ?>
<?php endif;?>
</body>
</html>
<?php $this->endPage() ?>
