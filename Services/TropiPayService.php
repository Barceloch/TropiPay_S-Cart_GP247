<?php
namespace App\GP247\Plugins\TropiPay\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TropiPayService
{
    private $clientId;
    private $clientSecret;
    private $apiUrl;
    private $accessToken;
    private $client;

    public function __construct($clientId, $clientSecret, $serverMode = 'Development')
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->apiUrl = $serverMode === 'Production' 
            ? 'https://www.tropipay.com/api/v2'
            : 'https://tropipay-dev.herokuapp.com/api/v2';
        $this->client = new Client(['base_uri' => $this->apiUrl, 'timeout' => 30]);
    }

    public function authenticate()
    {
        try {
            $response = $this->client->post('/access/token', [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => 'ALLOW_GET_PROFILE_DATA ALLOW_PAYMENT_IN ALLOW_EXTERNAL_CHARGE',
                ]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            $this->accessToken = $data['access_token'] ?? null;
            return $this->accessToken !== null;
        } catch (\Exception $e) {
            Log::error('TropiPay Auth Error: ' . $e->getMessage());
            return false;
        }
    }

    public function createPaymentCard($payload)
    {
        if (!$this->accessToken && !$this->authenticate()) {
            throw new \Exception('Failed to authenticate');
        }
        try {
            $response = $this->client->post('/paymentcards', [
                'headers' => ['Authorization' => 'Bearer ' . $this->accessToken, 'Content-Type' => 'application/json'],
                'json' => $payload
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('TropiPay Create Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function preparePaymentCardPayload($order, $urlSuccess, $urlFailed, $urlNotification)
    {
        return [
            'reference' => 'ORDER-' . $order->id,
            'concept' => 'Order #' . $order->id,
            'amount' => round($order->total * 100),
            'currency' => $order->currency ?? 'EUR',
            'description' => 'Payment for order #' . $order->id,
            'singleUse' => true,
            'reasonId' => 4,
            'expirationDays' => 7,
            'lang' => app()->getLocale() ?? 'es',
            'urlSuccess' => $urlSuccess,
            'urlFailed' => $urlFailed,
            'urlNotification' => $urlNotification,
            'client' => [
                'name' => $order->first_name ?? '',
                'lastName' => $order->last_name ?? '',
                'address' => $order->address1 ?? '',
                'phone' => $order->phone ?? '',
                'email' => $order->email ?? '',
                'countryId' => 1,
                'termsAndConditions' => true,
            ],
        ];
    }
}