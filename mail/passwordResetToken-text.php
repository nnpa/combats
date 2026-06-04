<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Url::to(['site/reset-password', 'token' => $user->password_reset_token], true);
?>

Здравствуйте, <?= $user->username ?>!

Вы получили это письмо, потому что запросили сброс пароля для вашей учетной записи.

Для сброса пароля перейдите по ссылке:
<?= $resetLink ?>

Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.

---
Это письмо было отправлено автоматически. Пожалуйста, не отвечайте на него.
© <?= date('Y') ?> Dungeon RPG