<?php
#App\GP247\Plugins\TropiPay\Controllers\FrontController.php
namespace App\GP247\Plugins\TropiPay\Controllers;

use App\GP247\Plugins\TropiPay\AppConfig;
use GP247\Front\Controllers\RootFrontController;
use App\GP247\Plugins\TropiPay\Services\TropiPayService;
use GP247\Core\Front\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontController extends RootFrontController
{
    public $plugin;
    private $tropiPayService;

    public function __construct()
    {
        parent::__construct();
        $this->plugin = new AppConfig;
        $this->tropiPayService = new TropiPayService(
            tropipay_get_config('client_id'),
            tropipay_get_config('client_secret'),
            tropipay_get_config('server_mode', 'Development')
        );
    }

    public function index() {
        return view($this->plugin->appPath.'::Front',
            [
                //
            ]
        );
    }

    public function processOrder(Request $request)
    {
        try {
            $orderId = session('orderID');
            if (!$orderId) {
                return redirect()->route('cart.checkout')->with('error', 'Order not found in session');
            }
            
            $order = ShopOrder::find($orderId);
            if (!$order) {
                return redirect()->route('cart.checkout')->with('error', 'Order not found');
            }

            $urlSuccess = route('tropipay.success', ['order_id' => $orderId]);
            $urlFailed = route('tropipay.failed', ['order_id' => $orderId]);
            $urlNotification = route('tropipay.webhook');
            
            $payload = $this->tropiPayService->preparePaymentCardPayload($order, $urlSuccess, $urlFailed, $urlNotification);
            $paymentCard = $this->tropiPayService->createPaymentCard($payload);

            if (isset($paymentCard['shortUrl'])) {
                $order->transaction = $paymentCard['shortUrl'];
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
            $order->payment_status = 'paid';
            $order->status = 'processing';
            $order->save();
            return (new \GP247\Shop\Controllers\ShopCartController)->completeOrder();
        }
        return redirect()->route('home')->with('error', 'Order not found');
    }

    public function failed(Request $request)
    {
        $order = ShopOrder::find($request->input('order_id'));
        if ($order) {
            $order->payment_status = 'failed';
            $order->save();
            return view($this->plugin->appPath.'::payment_failed', ['order' => $order, 'message' => 'Payment failed']);
        }
        return redirect()->route('home')->with('error', 'Order not found');
    }

    public function webhook(Request $request)
    {
        try {
            if (!$this->tropiPayService->validateSignature($request)) {
                Log::error('TropiPay Webhook: Invalid signature');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

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
