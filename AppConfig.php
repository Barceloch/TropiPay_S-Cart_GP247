<?php

namespace App\GP247\Plugins\TropiPay;

class AppConfig
{
    public function install()
    {
        // Aquí podrías insertar registros en la tabla de pagos de S‑Cart/GP247 si lo necesitas.
        return ['error' => 0, 'msg' => 'TropiPay instalado'];
    }

    public function uninstall()
    {
        return ['error' => 0, 'msg' => 'TropiPay desinstalado'];
    }

    public function enable()
    {
        // Normalmente se cambia el estado en config() o en la DB
        return ['error' => 0, 'msg' => 'TropiPay habilitado'];
    }

    public function disable()
    {
        return ['error' => 0, 'msg' => 'TropiPay deshabilitado'];
    }

    public function getInfo()
    {
        return [
            'name'        => 'TropiPay Payment Gateway',
            'code'        => 'TropiPay',
            'group'       => 'Payment',
            'version'     => '1.0.0',
            'author'      => 'Tu Nombre / Empresa',
            'link'        => 'https://tropipay.com',
            'config'      => include __DIR__ . '/config.php',
        ];
    }

    public function clickApp()
    {
        // Cuando haces click en el plugin desde el admin.
        return redirect()->route('tropipay.admin.config');
    }

    public function setupStore($storeId = null)
    {
        return ['error' => 0, 'msg' => 'OK'];
    }

    public function removeStore($storeId = null)
    {
        return ['error' => 0, 'msg' => 'OK'];
    }
}
