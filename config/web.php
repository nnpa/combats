<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'container' => [
        'singletons' => [
            \yii\mail\MailerInterface::class => [
                'class' => \yii\symfonymailer\Mailer::class,
                // send all mails to a file by default.
                'useFileTransport' => true,
                'viewPath' => '@app/mail',
            ],
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'mAF84vEB4xDZhJN2xfwIx94IZf5qnqDx',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => false,  // false для реальной отправки, true для теста (письма в файл)
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',       // SMTP сервер
                'username' => 'ibaleksandrov1988@gmail.com',  // Твой email
                'password' => 'kafs rloj qvhb dnjv',     // Пароль приложения (16 цифр)
                'port' => '587',                       // Порт (587 для TLS, 465 для SSL)
                'encryption' => 'tls',                 // tls или ssl
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'dungeon/loadmap' => 'dungeon/loadmap',
                'dungeon/loadplayer' => 'dungeon/loadplayer',
                'dungeon/loadmonsters' => 'dungeon/loadmonsters',
                'dungeon/saveplayer' => 'dungeon/saveplayer',
                'dungeon/battle' => 'dungeon/battle',
                'dungeon/start' => 'dungeon/start',
                'dungeon/exit' => 'dungeon/exit',
                'dungeon/index' => 'dungeon/index',
            ],
        ],
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {


    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
