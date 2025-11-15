<?php
if (!function_exists('tropipay_is_active')) {
    function tropipay_is_active() {
        try {
            return \GP247\Core\Models\AdminConfig::where('code', 'Payment')
                ->where('key', 'TropiPay_status')
                ->value('value') == 1;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('tropipay_get_config')) {
    function tropipay_get_config($key) {
        try {
            return \GP247\Core\Models\AdminConfig::where('code', 'Payment')
                ->where('key', 'TropiPay_' . $key)
                ->value('value');
        } catch (\Exception $e) {
            return null;
        }
    }
}