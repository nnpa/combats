<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Url::to(['site/reset-password', 'token' => $user->password_reset_token], true);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Сброс пароля</h1>
        </div>
        <div class="content">
            <p>Здравствуйте, <strong><?= Html::encode($user->username) ?></strong>!</p>
            
            <p>Вы получили это письмо, потому что запросили сброс пароля для вашей учетной записи.</p>
            
            <p>Для сброса пароля нажмите на кнопку ниже:</p>
            
            <p style="text-align: center;">
                <?= Html::a('Сбросить пароль', $resetLink, ['class' => 'button']) ?>
            </p>
            
            <p>Или скопируйте ссылку в браузер:</p>
            <p><small><?= Html::encode($resetLink) ?></small></p>
            
            <p>Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>
        </div>
        <div class="footer">
            <p>Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
            <p>&copy; <?= date('Y') ?> Dungeon RPG. Все права защищены.</p>
        </div>
    </div>
</body>
</html>