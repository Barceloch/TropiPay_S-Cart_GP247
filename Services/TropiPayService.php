<?php

namespace App\GP247\Plugins\TropiPay\Services;

use Exception;

class TropiPayService
{
    protected string $clientId;
    protected string $clientSecret;
    protected bool $sandbox;

    public function __construct()
    {
        $this->clientId     = env('TROPIPAY_CLIENT_ID', '');
        $this->clientSecret = env('TROPIPAY_CLIENT_SECRET', '');
        $this->sandbox      = (bool) env('TROPIPAY_SANDBOX', true);
    }

    protected function baseUrl(): string
    {
        return $this->sandbox
            ? 'https://sandbox.tropipay.me'
            : 'https://www.tropipay.com';
    }

    public function getAccessToken(): string
    {
        $url = $this->baseUrl() . '/api/v3/access/token';

        $payload = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'User-Agent: GP247-SCart-TropiPay/1.0',
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('TropiPay cURL token error: ' . curl_error($ch));
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) {
            throw new Exception('TropiPay token HTTP ' . $code . ' => ' . $response);
        }

        $data = json_decode($response, true);
        if (!isset($data['access_token'])) {
            throw new Exception('TropiPay token inválido');
        }

        return $data['access_token'];
    }

    public function createPaymentCard(array $params): array
    {
        $token = $this->getAccessToken();
        $url   = $this->baseUrl() . '/api/v3/paymentcards';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_POSTFIELDS     => json_encode($params),
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('TropiPay cURL payment error: ' . curl_error($ch));
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code < 200 || $code >= 300) {
            throw new Exception('TropiPay payment HTTP ' . $code . ' => ' . $response);
        }

        $data = json_decode($response, true);
        return $data;
    }
}
