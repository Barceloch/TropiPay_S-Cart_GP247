<?php

namespace App\GP247\Plugins\TropiPay\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExtensionModel extends Model
{
    protected $table = 'extensions';

    /**
     * Install extension - Setup configuration
     */
    public function installExtension()
    {
        try {
            // Just insert default configuration - no database table needed
            $this->insertDefaultConfig();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('TropiPay Extension Install Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Uninstall extension - Clean up configuration only
     */
    public function uninstallExtension()
    {
        try {
            // Clean up TropiPay configurations only
            \GP247\Core\Models\AdminConfig::where('group', 'TropiPay')
                ->where('code', 'TropiPay')
                ->delete();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('TropiPay Extension Uninstall Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Insert default configuration
     */
    private function insertDefaultConfig()
    {
        $configData = [
            [
                'group' => 'TropiPay',
                'code' => 'TropiPay',
                'key' => 'TropiPay_client_id',
                'value' => '',
                'sort' => 0,
                'store_id' => 0,
                'detail' => 'plugins/TropiPay::lang.client_id',
            ],
            [
                'group' => 'TropiPay',
                'code' => 'TropiPay',
                'key' => 'TropiPay_client_secret',
                'value' => '',
                'sort' => 1,
                'store_id' => 0,
                'detail' => 'plugins/TropiPay::lang.client_secret',
            ],
            [
                'group' => 'TropiPay',
                'code' => 'TropiPay',
                'key' => 'TropiPay_sandbox',
                'value' => '0',
                'sort' => 2,
                'store_id' => 0,
                'detail' => 'plugins/TropiPay::lang.sandbox_mode',
            ],
            [
                'group' => 'TropiPay',
                'code' => 'TropiPay',
                'key' => 'TropiPay_enabled',
                'value' => '0',
                'sort' => 3,
                'store_id' => 0,
                'detail' => 'plugins/TropiPay::lang.enabled',
            ],
            [
                'group' => 'Plugins',
                'code' => 'TropiPay_config',
                'key' => 'TropiPay_order_status_success',
                'value' => '2',
                'sort' => 4,
                'store_id' => 0,
                'detail' => 'plugins/TropiPay::lang.order_status_success',
            ],
            [
                'group' => 'Plugins',
                'code' => 'TropiPay_config',
                'key' => 'TropiPay_payment_status_success',
                'value' => '3',
                'sort' => 5,
                'store_id' => 0,
                'detail' => 'plugins/TropiPay::lang.payment_status_success',
            ],
            [
                'group' => 'Plugins',
                'code' => 'TropiPay_config',
                'key' => 'TropiPay_default_currency',
                'value' => 'USD',
                'sort' => 6,
                'store_id' => 0,
                'detail' => 'plugins/TropiPay::lang.default_currency',
            ],
        ];

        foreach ($configData as $config) {
            $exists = \GP247\Core\Models\AdminConfig::where('group', $config['group'])
                ->where('code', $config['code'])
                ->where('key', $config['key'])
                ->exists();

            if (!$exists) {
                \GP247\Core\Models\AdminConfig::insert($config);
            }
        }
    }
}
