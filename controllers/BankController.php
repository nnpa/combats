<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\User;

class BankController extends Controller
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

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $user = User::findOne($userId);
        
        return $this->render('index', [
            'user' => $user,
        ]);
    }

    public function actionConvert()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $amount = $request->post('amount');
        $userId = Yii::$app->user->id;
        
        if (!$amount || $amount <= 0) {
            return ['success' => false, 'message' => 'Введите корректное количество EKR'];
        }
        
        $amount = (int)$amount;
        $rate = 30; // Курс 1 EKR = 30 KR
        
        $user = User::findOne($userId);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }
        
        if ($user->ekr < $amount) {
            return ['success' => false, 'message' => 'Недостаточно EKR. У вас: ' . $user->ekr . ' EKR'];
        }
        
        $krGain = $amount * $rate;
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $user->ekr -= $amount;
            $user->kr += $krGain;
            
            if (!$user->save(false)) {
                throw new \Exception('Ошибка при конвертации');
            }
            
            $transaction->commit();
            
            return [
                'success' => true, 
                'message' => "Успешно конвертировано! Вы получили {$krGain} KR",
                'newEkr' => $user->ekr,
                'newKr' => $user->kr
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Ошибка при конвертации: ' . $e->getMessage()];
        }
    }
}