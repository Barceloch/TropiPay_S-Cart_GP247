<?php
namespace App\GP247\Plugins\TropiPay\Controllers;

use App\Http\Controllers\Controller;
use App\GP247\Plugins\TropiPay\Services\TropiPayService;
use GP247\Core\Front\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private function getTropiPayService()
    {
        $clientId = tropipay_get_config('client_id');
        $clientSecret = tropipay_get_config('client_secret');
        $serverMode = tropipay_get_config('server_mode', 'Development');
        return new TropiPayService($clientId, $clientSecret, $serverMode);
    }

    public function process(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $order = ShopOrder::find($orderId);
            if (!$order) {
                return redirect()->route('cart.checkout')->with('error', 'Order not found');
            }

            $service = $this->getTropiPayService();
            $urlSuccess = route('tropipay.success', ['order_id' => $orderId]);
            $urlFailed = route('tropipay.failed', ['order_id' => $orderId]);
            $urlNotification = route('tropipay.webhook');
            
            $payload = $service->preparePaymentCardPayload($order, $urlSuccess, $urlFailed, $urlNotification);
            $paymentCard = $service->createPaymentCard($payload);

            if (isset($paymentCard['shortUrl'])) {
                $order->transaction_id = $paymentCard['shortUrl'];
                $order->payment_status = 'pending';
                $order->save();
                
                $paymentUrl = $paymentCard['paymentUrl'] ?? 'https://www.tropipay.com/pay/' . $paymentCard['shortUrl'];
                return redirect($paymentUrl);
            }
            throw new \Exception('Failed to create payment link');
        } catch (\Exception $e) {
            Log::error('TropiPay Process Error: ' . $e->getMessage());
            return redirect()->route('cart.checkout')->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $order = ShopOrder::find($request->input('order_id'));
        if ($order) {
            return view('tropipay::payment_success', ['order' => $order, 'message' => 'Payment processing...']);
        }
        return redirect()->route('home')->with('error', 'Order not found');
    }

    public function failed(Request $request)
    {
        $order = ShopOrder::find($request->input('order_id'));
        if ($order) {
            $order->payment_status = 'failed';
            $order->save();
            return view('tropipay::payment_failed', ['order' => $order, 'message' => 'Payment failed']);
        }
        return redirect()->route('home')->with('error', 'Order not found');
    }

    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('TropiPay Webhook:', $payload);
            
            $reference = $payload['reference'] ?? null;
            if (!$reference) {
                return response()->json(['error' => 'No reference'], 400);
            }
            
            $orderId = str_replace('ORDER-', '', $reference);
            $order = ShopOrder::find($orderId);
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }
            
            $status = $payload['status'] ?? null;
            switch ($status) {
                case 2: $order->payment_status = 'paid'; $order->status = 'processing'; break;
                case 3: $order->payment_status = 'failed'; break;
                case 4: $order->payment_status = 'cancelled'; break;
            }
            $order->save();
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('TropiPay Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}