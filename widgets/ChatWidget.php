<?php
namespace app\widgets;

use Yii;
use yii\base\Widget;

class ChatWidget extends Widget
{
    public function run()
    {
        $user = Yii::$app->user->identity;
        
        return $this->render('chat', [
            'user' => $user,
        ]);
    }
}