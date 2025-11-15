<?php

namespace App\GP247\Plugins\TropiPay\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TropiPayAdminController extends Controller
{
    public function index()
    {
        $config = include __DIR__ . '/../config.php';

        return view('tropipay::admin.config', [
            'title'  => 'Configuración TropiPay',
            'config' => $config,
        ]);
    }

    public function save(Request $request)
    {
        // EJEMPLO: aquí podrías guardar la configuración en config/plugin o en la DB.
        // Para mantener simple, solo mostramos los datos enviados.
        $data = $request->only(['sandbox']);

        return redirect()
            ->back()
            ->with('success', 'Configuración de TropiPay guardada (ejemplo, ajusta para persistir realmente).');
    }
}
