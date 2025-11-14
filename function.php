<?php
if (!function_exists('tropipay_is_active')) {
    function tropipay_is_active() {
        try {
            return \GP247\Core\Admin\Models\AdminConfig::where('code', 'TropiPay')
                ->where('key', 'status')->value('value') == 1;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('tropipay_get_config')) {
    function tropipay_get_config($key = null, $default = null) {
        try {
            if ($key) {
                return \GP247\Core\Admin\Models\AdminConfig::where('code', 'TropiPay')
                    ->where('key', $key)->value('value') ?? $default;
            }
            return \GP247\Core\Admin\Models\AdminConfig::where('code', 'TropiPay')
                ->pluck('value', 'key')->toArray();
        } catch (\Exception $e) {
            return $default;
        }
    }
}