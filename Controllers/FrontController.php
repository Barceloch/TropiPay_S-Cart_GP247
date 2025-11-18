<?php
#App\GP247\Plugins\TropiPay\Controllers\FrontController.php

namespace App\GP247\Plugins\TropiPay\Controllers;

use GP247\Front\Controllers\RootFrontController;
use App\GP247\Plugins\TropiPay\AppConfig;
use App\GP247\Plugins\TropiPay\Services\TropiPayService;
use Illuminate\Support\Facades\Log;

class FrontController extends RootFrontController
{
    protected $tropiPayService;
    public $plugin;

    public function __construct()
    {
        parent::__construct();
        $this->plugin = new AppConfig;
        $this->tropiPayService = new TropiPayService();
    }

    /**
     * Get callback URL compatible with TropiPay
     * Uses TUNNEL_URL if configured, otherwise uses normal store URL
     */
    private function getCallbackUrl($type, $orderId = null)
    {
        $tunnelUrl = env('TUNNEL_URL');
        
        if ($tunnelUrl) {
            // Use tunnel URL if configured (for local development)
            gp247_report('TropiPay using TUNNEL_URL: ' . $tunnelUrl);
            switch ($type) {
                case 'success':
                    return $tunnelUrl . '/tropipay/success/' . $orderId;
                case 'error':
                    return $tunnelUrl . '/tropipay/error/' . $orderId;
                case 'webhook':
                    return $tunnelUrl . '/tropipay/webhook';
            }
        } else {
            // Use normal store URL if no tunnel configured
            gp247_report('TropiPay using normal store URLs');
            switch ($type) {
                case 'success':
                    return secure_url(route('tropipay.success', ['order_id' => $orderId]));
                case 'error':
                    return secure_url(route('tropipay.error', ['order_id' => $orderId]));
                case 'webhook':
                    return secure_url(route('tropipay.webhook'));
            }
        }
        
        return route('front.home');
    }

    /**
     * Process order - Create TropiPay payment link and redirect
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processOrder()
    {
        $data = session()->all();
        if (empty($data['orderID']) || empty($data['dataOrder']) || empty($data['arrCartDetail'])) {
            gp247_report('TropiPay Process Order - Missing required session data: ' . json_encode($data));
            return redirect()->route('front.home')->with('error', 'Missing required order data');
        }

        $orderID = $data['orderID'];
        $dataOrder = $data['dataOrder'];
        $arrCartDetail = $data['arrCartDetail'];

        $order = \GP247\Shop\Models\ShopOrder::find($orderID);
        if (!$order) {
            gp247_report('TropiPay Process Order - Order not found: ' . $orderID);
            return redirect()->route('front.home')->with('error', 'Order not found');
        }

        try {
            gp247_report('TropiPay Process Order - Starting process for order ID: ' . $orderID);
            
            // Prepare payment data
            $urlSuccess = $this->getCallbackUrl('success', $orderID);
            $urlFailed = $this->getCallbackUrl('error', $orderID);
            $urlNotification = $this->getCallbackUrl('webhook');

            gp247_report('TropiPay Process Order - Generated URLs: success=' . $urlSuccess . ', failed=' . $urlFailed . ', webhook=' . $urlNotification);

            $paymentData = [
                'reference' => 'ORDER-' . $orderID . '-' . time(),
                'concept' => 'Order #' . $orderID,
                'favorite' => false,
                'amount' => $dataOrder['total'],
                'currency' => gp247_config('TropiPay_default_currency', 'USD'),
                'description' => 'Payment for order #' . $orderID,
                'singleUse' => true,
                'reasonId' => 4, // eCommerce
                'expirationDays' => 1,
                'lang' => app()->getLocale(),
                'urlSuccess' => $urlSuccess,
                'urlFailed' => $urlFailed,
                'urlNotification' => $urlNotification,
                'serviceDate' => now()->format('Y-m-d H:i:s')
            ];

            gp247_report('TropiPay Process Order - Payment data prepared: ' . json_encode($paymentData));

            // Create payment link
            $result = $this->tropiPayService->createPaymentLink($paymentData);

            gp247_report('TropiPay Process Order - API Response: ' . json_encode($result));

            if (empty($result['shortUrl'])) {
                gp247_report('TropiPay Process Order - Failed to create payment link: ' . json_encode($result));
                return redirect()->route('front.home')->with('error', 'Failed to create TropiPay payment link');
            }

            // Store payment link ID and reference in session
            session(['tropiPayLinkId' => $result['id'] ?? null]);
            session(['tropiPayShortUrl' => $result['shortUrl']]);
            gp247_report('TropiPay Process Order - Redirecting to payment URL: ' . $result['shortUrl']);

            // Redirect to TropiPay payment page
            return redirect()->away($result['shortUrl']);

        } catch (\Exception $e) {
            gp247_report('TropiPay Process Order - Error: ' . $e->getMessage());
            return redirect()->route('front.home')->with('error', 'TropiPay process for order ' . $orderID . ' failed. Please contact administrator.');
        }
    }

    /**
     * Handle successful payment
     */
    public function success()
    {
        $orderID = request()->order_id;
        
        if (!$orderID) {
            return redirect()->route('front.home')->with('error', 'Invalid order');
        }

        $order = \GP247\Shop\Models\ShopOrder::find($orderID);
        if (!$order) {
            return redirect()->route('front.home')->with('error', 'Order not found');
        }

        // Update order status - TropiPay payment successful
        $order->update([
            'status' => gp247_config($this->plugin->configKey.'_order_status_success'),
            'payment_status' => gp247_config($this->plugin->configKey.'_payment_status_success')
        ]);

        gp247_report('TropiPay Success - Order marked as paid: ' . $orderID);

        // Add order history
        $dataHistory = [
            'order_id' => $orderID,
            'content' => 'Payment completed via TropiPay',
            'customer_id' => $order->customer_id ?? 0,
            'order_status_id' => gp247_config($this->plugin->configKey.'_order_status_success'),
        ];
        $order->addOrderHistory($dataHistory);

        // Clear session
        session()->forget('tropiPayLinkId');

        return (new \GP247\Shop\Controllers\ShopCartController)->completeOrder();
    }

    /**
     * Handle failed/cancelled payment
     */
    public function error()
    {
        $orderID = request()->order_id;
        
        if ($orderID) {
            $order = \GP247\Shop\Models\ShopOrder::find($orderID);
            if ($order) {
                $order->update([
                    'status' => 4, // Failed status
                    'payment_status' => 4 // Failed payment status
                ]);
                
                gp247_report('TropiPay Error - Order marked as failed: ' . $orderID);
                
                // Add order history
                $dataHistory = [
                    'order_id' => $orderID,
                    'content' => 'Payment failed/cancelled via TropiPay',
                    'customer_id' => $order->customer_id ?? 0,
                    'order_status_id' => 4,
                ];
                $order->addOrderHistory($dataHistory);
            }
        }
        
        return (new \GP247\Shop\Controllers\ShopCartController)->cancelOrder();
    }

    /**
     * Handle TropiPay webhook for payment notifications
     */
    public function webhook()
    {
        try {
            $data = request()->all();
            gp247_report('TropiPay Webhook received: ' . json_encode($data));
            
            // Process webhook data based on payment status
            if (isset($data['status']) && $data['status'] === 'success') {
                // Payment successful - update order status
                $orderId = $data['order_id'] ?? null;
                if ($orderId) {
                    $order = \GP247\Shop\Models\ShopOrder::find($orderId);
                    if ($order) {
                        $order->update([
                            'status' => gp247_config($this->plugin->configKey.'_order_status_success'),
                            'payment_status' => gp247_config($this->plugin->configKey.'_payment_status_success')
                        ]);
                        
                        gp247_report('TropiPay Webhook - Order marked as paid: ' . $orderId);
                        
                        $dataHistory = [
                            'order_id' => $orderId,
                            'content' => 'Payment completed via TropiPay webhook',
                            'customer_id' => $order->customer_id ?? 0,
                            'order_status_id' => gp247_config($this->plugin->configKey.'_order_status_success'),
                        ];
                        $order->addOrderHistory($dataHistory);
                    }
                }
            }
            
            return response('OK', 200);
        } catch (\Exception $e) {
            gp247_report('TropiPay Webhook error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Check order status (AJAX helper)
     */

    /**
     * Get payment status (AJAX helper)
     */
    public function getStatus()
    {
        // Get order from session or request
        $orderID = session('orderID') ?? request('order_id');
        
        if (!$orderID) {
            return response()->json(['status' => 'error', 'message' => 'Order not found']);
        }
        
        $order = \GP247\Shop\Models\ShopOrder::find($orderID);
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found']);
        }
        
        return response()->json([
            'status' => 'success',
            'payment_status' => $order->payment_status,
            'status_name' => $this->getStatusName($order->payment_status)
        ]);
    }

    /**
     * Cancel pending payment (AJAX helper)
     */
    public function cancelPending()
    {
        // Get order from session or request
        $orderID = session('orderID') ?? request('order_id');
        
        if (!$orderID) {
            return response()->json(['status' => 'error', 'message' => 'Order not found']);
        }
        
        $order = \GP247\Shop\Models\ShopOrder::find($orderID);
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found']);
        }
        
        // Update order status to cancelled
        $order->update([
            'status' => 4, // Cancelled status
            'payment_status' => 4 // Cancelled payment status
        ]);
        
        gp247_report('TropiPay - Order cancelled: ' . $orderID);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Order cancelled successfully'
        ]);
    }
    
    /**
     * Helper method to get status name
     */
    private function getStatusName($statusId)
    {
        $statuses = [
            1 => 'Pending',
            2 => 'Processing', 
            3 => 'Paid',
            4 => 'Cancelled',
            5 => 'Failed'
        ];
        
        return $statuses[$statusId] ?? 'Unknown';
    }
}
