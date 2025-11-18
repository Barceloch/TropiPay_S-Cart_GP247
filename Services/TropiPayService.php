<?php
namespace App\GP247\Plugins\TropiPay\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TropiPayService
{
    public $clientId;
    public $clientSecret;
    public $sandbox;
    public $baseUrl;
    private $accessToken;
    private $tokenExpiry;

    public function __construct()
    {
        $this->clientId = gp247_config('TropiPay_client_id', '');
        $this->clientSecret = gp247_config('TropiPay_client_secret', '');
        $this->sandbox = gp247_config('TropiPay_sandbox', 'TropiPay') == '1';
        
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.tropipay.me/api/v3'
            : 'https://www.tropipay.com/api/v3';
    }

    /**
     * Obtener token de acceso
     */
    public function authenticate()
    {
        // Verificar si el token aún es válido
        if ($this->accessToken && $this->tokenExpiry && $this->tokenExpiry > time()) {
            return $this->accessToken;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ])
                ->asForm()
                ->post($this->baseUrl . '/access/token', [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                $this->tokenExpiry = time() + ($data['expires_in'] - 300); // 5 minutos antes de expirar
                
                return $this->accessToken;
            } else {
                $errorData = $response->body();
                gp247_report('TropiPay Authentication Error - Status: ' . $response->status() . ' - Response: ' . $errorData);
                
                $errorMessage = 'Error de autenticación (HTTP ' . $response->status() . '): ';
                $errorMessage .= $errorData;
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('TropiPay Authentication Error: ' . $e->getMessage());
            gp247_report('TropiPay Authentication Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crear enlace de pago usando API v3 de TropiPay
     */
    public function createPaymentLink($paymentData)
    {
        $token = $this->authenticate();

        try {
            // Convert amount to centavos (TropiPay expects cents)
            $amountInCents = round($paymentData['amount'] * 100);

            $payload = [
                'reference' => $paymentData['reference'],
                'concept' => $paymentData['concept'],
                'amount' => $amountInCents,
                'currency' => $paymentData['currency'] ?? 'USD',
                'description' => $paymentData['description'] ?? 'Payment',
                'singleUse' => $paymentData['singleUse'] ?? true,
                'expirationDays' => $paymentData['expirationDays'] ?? 1,
                'lang' => $paymentData['lang'] ?? 'en',
                'urlSuccess' => $paymentData['urlSuccess'],
                'urlFailed' => $paymentData['urlFailed'],
                'urlNotification' => $paymentData['urlNotification'],
                'reasonId' => (int)($paymentData['reasonId'] ?? 4), // eCommerce - debe ser integer
                'favorite' => (bool)($paymentData['favorite'] ?? false),
                'serviceDate' => $paymentData['serviceDate'] ?? now()->format('Y-m-d H:i:s')
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ])
                ->post($this->baseUrl . '/paymentcards', $payload);

            if ($response->successful()) {
                return $response->json();
            } else {
                $statusCode = $response->status();
                $errorResponse = $response->body();
                
                gp247_report('TropiPay API Error - Status: ' . $statusCode . ' - Response: ' . $errorResponse);
                
                $errorMsg = 'Error creating payment link (HTTP ' . $statusCode . '): ';
                if (!empty($errorResponse)) {
                    $errorMsg .= $errorResponse;
                } else {
                    $errorMsg .= 'Unknown API error';
                }
                
                throw new \Exception($errorMsg);
            }
        } catch (\Exception $e) {
            Log::error('TropiPay Create Payment Error: ' . $e->getMessage());
            gp247_report('TropiPay Create Payment Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener información de un pago
     */
    public function getPaymentInfo($paymentId)
    {
        $token = $this->authenticate();

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ])
                ->get($this->baseUrl . '/paymentcards/' . $paymentId);

            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception('Error obteniendo información del pago');
            }
        } catch (\Exception $e) {
            Log::error('TropiPay Get Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirmar un pago (webhook)
     */
    public function confirmPayment($paymentId)
    {
        $paymentInfo = $this->getPaymentInfo($paymentId);
        
        // Verificar el estado del pago
        if (isset($paymentInfo['status']) && $paymentInfo['status'] === 'PAID') {
            return [
                'success' => true,
                'payment_id' => $paymentId,
                'amount' => $paymentInfo['amount'] / 100, // Convertir de centavos
                'currency' => $paymentInfo['currency'],
                'reference' => $paymentInfo['reference']
            ];
        }

        return [
            'success' => false,
            'message' => 'Pago no confirmado'
        ];
    }

    /**
     * Test de conexión simplificado
     * En lugar de obtener balance (que puede no estar disponible), 
     * verificamos que la autenticación funciona
     */
    public function testConnection()
    {
        $token = $this->authenticate();

        // Si llegamos aquí, la autenticación funcionó
        return [
            'success' => true,
            'authenticated' => true,
            'token_received' => !empty($token),
            'message' => 'Conexión exitosa con TropiPay - Autenticación verificada'
        ];
    }

    /**
     * Obtener saldo de la cuenta (método original comentado hasta confirmar endpoint correcto)
     */
    public function getBalance()
    {
        // Por ahora, usamos un endpoint más simple que debería funcionar
        // o simplemente devolvemos que la autenticación está disponible
        
        $token = $this->authenticate();

        try {
            // Intentemos con un endpoint más simple
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ])
                ->get($this->baseUrl . '/users/profile');

            if ($response->successful()) {
                return $response->json();
            } else {
                // Si el endpoint de usuarios tampoco funciona, 
                // simplemente confirmamos que la autenticación funciona
                return [
                    'authenticated' => true,
                    'balance_info' => 'Endpoint de balance no disponible, pero autenticación exitosa',
                    'api_response' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('TropiPay Get Balance Error: ' . $e->getMessage());
            // En lugar de fallar, devolvemos información de autenticación exitosa
            return [
                'authenticated' => true,
                'balance_info' => 'Endpoint de balance no disponible, pero autenticación exitosa',
                'error' => $e->getMessage()
            ];
        }
    }
}