<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use YooKassa\Client;
use YooKassa\Helpers\Webhook;

class PaymentController extends Controller
{
    // CSRF отключаем только для вебхука, для остальных экшенов он включен
    public function beforeAction($action)
    {
        if ($action->id === 'webhook') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'success'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['webhook'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'webhook' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Страница оплаты (представление)
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Создание платежа (AJAX)
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->user->id;
        if (!$userId) {
            return ['success' => false, 'error' => 'Не авторизован'];
        }

        $price = 900.00;
        $currencyAmount = 30; // EKR

        $client = new Client();
        $client->setAuth(Yii::$app->params['yookassa.shopId'], Yii::$app->params['yookassa.secretKey']);

        try {
            $idempotenceKey = uniqid('', true);
            $payment = $client->createPayment(
                [
                    'amount' => [
                        'value' => $price,
                        'currency' => 'RUB',
                    ],
                    'confirmation' => [
                        'type' => 'embedded',
                    ],
                    'capture' => true,
                    'description' => 'Покупка 30 EKR',
                    'metadata' => [
                        'user_id' => $userId,
                        'product' => 'ekr',
                        'amount_currency' => $currencyAmount,
                        'price' => $price,
                    ],
                ],
                $idempotenceKey
            );

            return [
                'success' => true,
                'confirmation_token' => $payment->getConfirmation()->getConfirmationToken(),
                'payment_id' => $payment->getId(),
            ];
        } catch (\Exception $e) {
            Yii::error('Payment creation error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Ошибка создания платежа'];
        }
    }

    /**
     * Вебхук для уведомлений
     */
    public function actionWebhook()
    {
        $rawBody = Yii::$app->request->rawBody;
        $data = json_decode($rawBody, true);
        $headers = Yii::$app->request->headers;

        // Проверка подписи
        if (!$this->validateWebhookSignature($rawBody, $headers)) {
            Yii::error('Invalid webhook signature');
            return 'Invalid signature';
        }

        if ($data['event'] === 'payment.succeeded') {
            $payment = $data['object'];
            $metadata = $payment['metadata'] ?? [];
            $userId = $metadata['user_id'] ?? null;
            $product = $metadata['product'] ?? null;
            $currencyAmount = (int)($metadata['amount_currency'] ?? 0);
            $paymentId = $payment['id'];

            if ($userId && $product === 'ekr' && $currencyAmount > 0) {
                // Предотвращение повторной обработки
                $cacheKey = 'payment_processed_' . $paymentId;
                if (Yii::$app->cache->get($cacheKey)) {
                    Yii::info("Payment $paymentId already processed");
                    return 'OK';
                }

                $user = \app\models\User::findOne($userId);
                if ($user) {
                    $user->ekr += $currencyAmount;
                    if ($user->save(false)) {
                        Yii::$app->cache->set($cacheKey, true, 86400);
                        Yii::info("User $userId received $currencyAmount EKR (payment $paymentId)");
                    } else {
                        Yii::error("Cannot save user $userId after payment $paymentId");
                    }
                } else {
                    Yii::error("User $userId not found for payment $paymentId");
                }
            } else {
                Yii::error("Invalid metadata for payment $paymentId: " . json_encode($metadata));
            }
        }

        return 'OK';
    }

    /**
     * Страница успеха
     */
    public function actionSuccess()
    {
        return $this->render('success');
    }

    /**
     * Проверка подписи вебхука
     */
    private function validateWebhookSignature($rawBody, $headers)
    {
        $signature = $headers->get('Webhook-Signature');
        if (!$signature) {
            return false;
        }
        try {
            return Webhook::checkSignature($rawBody, $signature, Yii::$app->params['yookassa.secretKey']);
        } catch (\Exception $e) {
            Yii::error('Signature check exception: ' . $e->getMessage());
            return false;
        }
    }
}