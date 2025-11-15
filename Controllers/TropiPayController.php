<?php

namespace App\GP247\Plugins\TropiPay\Controllers;

use App\GP247\Plugins\TropiPay\Services\TropiPayService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class TropiPayController extends Controller
{
    public function startPayment($orderId, TropiPayService $service)
    {
        // Aquí deberías recuperar el pedido real de S‑Cart/GP247:
        // $order = \GP247\Shop\Models\ShopOrder::findOrFail($orderId);
        // Para el ejemplo, asumimos datos mínimos:
        $order = [
            'id'     => $orderId,
            'total'  => 100.00,
            'email'  => 'cliente@example.com',
            'name'   => 'Cliente de prueba',
            'currency' => 'EUR',
        ];

        $params = [
            'concept'         => 'Pedido #' . $order['id'],
            'amount'          => $order['total'],
            'currency'        => $order['currency'],
            'singleUse'       => true,
            'expirationDays'  => 1,
            'urlSuccess'      => route('tropipay.return', ['status' => 'success', 'order' => $order['id']]),
            'urlFailed'       => route('tropipay.return', ['status' => 'failed', 'order' => $order['id']]),
            'urlNotification' => route('tropipay.webhook'),
            'lang'            => 'es',
            'payer'           => [
                'name'  => $order['name'],
                'email' => $order['email'],
            ],
        ];

        try {
            $payment = $service->createPaymentCard($params);

            // TropiPay suele devolver una URL o shortUrl para redirigir al cliente
            $redirectUrl = $payment['url'] ?? ($payment['shortUrl'] ?? null);

            if (!$redirectUrl) {
                return redirect()->back()->with('error', 'No se pudo obtener la URL de pago de TropiPay.');
            }

            // Aquí podrías guardar en la DB el id de la tarjeta / referencia
            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            Log::error('TropiPay startPayment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al iniciar el pago con TropiPay.');
        }
    }

    public function handleReturn(Request $request)
    {
        $status  = $request->get('status');
        $orderId = $request->get('order');

        // Aquí deberías actualizar el estado real del pedido:
        // if ($status === 'success') { ... }

        if ($status === 'success') {
            $msg = 'Pago realizado correctamente en TropiPay para el pedido #' . $orderId;
        } else {
            $msg = 'El pago en TropiPay fue cancelado o fallido para el pedido #' . $orderId;
        }

        return view('tropipay::front.return', [
            'status'   => $status,
            'orderId'  => $orderId,
            'message'  => $msg,
        ]);
    }

    public function webhook(Request $request)
    {
        // Aquí simplemente registramos el webhook.
        // En producción, valida la firma y actualiza pedidos según el payload.
        Log::info('TropiPay webhook recibido', ['payload' => $request->all()]);

        return response()->json(['ok' => true]);
    }
}
