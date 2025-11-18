<?php

/**
 * Functions for TropiPay Payment Plugin
 */

/**
 * Show TropiPay payment form
 */
function renderTropiPayPayment()
{
    $orderId = session('order_id');
    $order = \GP247\Shop\Models\ShopOrder::find($orderId);
    
    if (!$order) {
        return '';
    }

    $config = gp247_config_all('TropiPay');
    $enabled = gp247_config('tropipay_enabled', 'TropiPay') == '1';
    
    if (!$enabled) {
        return '';
    }

    return view('TropiPay::Front', compact('order', 'config'))->render();
}

/**
 * Process TropiPay payment
 */
function processTropiPayPayment($orderId)
{
    $order = \GP247\Shop\Models\ShopOrder::find($orderId);
    if (!$order) {
        return ['error' => 1, 'msg' => 'Orden no encontrada'];
    }

    try {
        $service = new \App\GP247\Plugins\TropiPay\Services\TropiPayService();
        
        $returnUrls = [
            'success' => url('/plugin/tropipay/return?order_id=' . $orderId . '&status=success'),
            'failed' => url('/plugin/tropipay/return?order_id=' . $orderId . '&status=failed'),
            'notification' => url('/tropipay/webhook')
        ];

        $paymentData = $service->createPaymentLink($order, $returnUrls);
        
        if (isset($paymentData['shortUrl'])) {
            return [
                'error' => 0, 
                'payment_url' => $paymentData['shortUrl'],
                'payment_id' => $paymentData['id'] ?? null
            ];
        } else {
            return ['error' => 1, 'msg' => 'Error creando enlace de pago'];
        }
        
    } catch (\Exception $e) {
        \Log::error('TropiPay Process Error: ' . $e->getMessage());
        return ['error' => 1, 'msg' => $e->getMessage()];
    }
}

/**
 * Check if TropiPay is available
 */
function isTropiPayAvailable()
{
    $clientId = gp247_config('tropipay_client_id', 'TropiPay');
    $clientSecret = gp247_config('tropipay_client_secret', 'TropiPay');
    $enabled = gp247_config('tropipay_enabled', 'TropiPay') == '1';
    
    return $enabled && !empty($clientId) && !empty($clientSecret);
}

/**
 * Get TropiPay payment title
 */
function getTropiPayTitle()
{
    $locale = app()->getLocale();
    if ($locale == 'es') {
        return 'Pagar con TropiPay';
    }
    return 'Pay with TropiPay';
}
