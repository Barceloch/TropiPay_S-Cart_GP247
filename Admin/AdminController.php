<?php
#App\GP247\Plugins\TropiPay\Admin\AdminController.php

namespace App\GP247\Plugins\TropiPay\Admin;

use GP247\Core\Controllers\RootAdminController;
use App\GP247\Plugins\TropiPay\AppConfig;
use Illuminate\Support\Facades\DB;

class AdminController extends RootAdminController
{
    public $plugin;

    public function __construct()
    {
        parent::__construct();
        $this->plugin = new AppConfig;
    }

    public function index()
    {
        $orderStatusSuccess = \GP247\Shop\Models\ShopOrderStatus::getIdAll();
        $paymentStatusSuccess = \GP247\Shop\Models\ShopPaymentStatus::getIdAll();
        
        // Get TropiPay order statistics
        $stats = $this->getTropiPayOrderStats();
        $recentOrders = $this->getRecentTropiPayOrders();
        
        // Get TropiPay orders with filtering
        $query = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay');
        
        // Apply filters
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        if (request('order_id')) {
            $query->where('id', request('order_id'));
        }
        
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $config = [
            'tropipay_client_id' => $this->getConfigValue('TropiPay_client_id', ''),
            'tropipay_client_secret' => $this->getConfigValue('TropiPay_client_secret', ''),
            'tropipay_sandbox' => $this->getConfigValue('TropiPay_sandbox', '0'),
            'tropipay_enabled' => $this->getConfigValue('TropiPay_enabled', '0'),
            'tropipay_default_currency' => $this->getConfigValue('TropiPay_default_currency', 'USD'),
        ];
        
        return view($this->plugin->appPath.'::admin.dashboard', [
            'plugin' => $this->plugin,
            'storeId' => session('adminStoreId'),
            'orderStatusSuccess' => $orderStatusSuccess,
            'paymentStatusSuccess' => $paymentStatusSuccess,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'orders' => $orders,
            'config' => $config,
            'urlUpdateConfig' => gp247_route_admin('admin_config.update'),
            'urlUpdateConfigGlobal' => gp247_route_admin('admin_config_global.update'),
        ]);
    }

    public function settings()
    {
        $config = [
            'tropipay_client_id' => $this->getConfigValue('TropiPay_client_id', ''),
            'tropipay_client_secret' => $this->getConfigValue('TropiPay_client_secret', ''),
            'tropipay_sandbox' => $this->getConfigValue('TropiPay_sandbox', '0'),
            'tropipay_enabled' => $this->getConfigValue('TropiPay_enabled', '0'),
            'tropipay_default_currency' => $this->getConfigValue('TropiPay_default_currency', 'USD'),
        ];
        
        return view($this->plugin->appPath.'::admin.settings', [
            'config' => $config,
            'title' => gp247_language_render($this->plugin->appPath.'::lang.plugin_settings'),
            'plugin' => $this->plugin,
        ]);
    }

    public function updateSettings()
    {
        $config = request()->only([
            'tropipay_client_id',
            'tropipay_client_secret',
            'tropipay_sandbox',
            'tropipay_enabled',
            'tropipay_default_currency'
        ]);

        // Handle checkbox values (unchecked checkboxes don't submit values)
        if (!isset($config['tropipay_sandbox'])) {
            $config['tropipay_sandbox'] = '0';
        }
        if (!isset($config['tropipay_enabled'])) {
            $config['tropipay_enabled'] = '0';
        }

        try {
            foreach ($config as $key => $value) {
                // Convert key to match database format: TropiPay_client_id (mixed case)
                // Convert tropipay_default_currency -> TropiPay_default_currency
                $dbKey = preg_replace_callback('/^tropipay_/', function($matches) {
                    return 'TropiPay_';
                }, $key);
                
                // Determine the correct group and code based on the key
                if (str_contains($dbKey, '_default_currency')) {
                    // Currency configuration is stored in the Plugins group with TropiPay code
                    $group = 'Plugins';
                    $code = 'TropiPay';
                } else {
                    // Other TropiPay configurations are stored in the TropiPay group
                    $group = 'TropiPay';
                    $code = 'TropiPay';
                }
                
                // Use raw SQL to check if value has changed (as requested by user)
                $existingResult = DB::select(
                    "SELECT value FROM gp247_admin_config WHERE `group` = ? AND `code` = ? AND `key` = ? AND store_id = ? LIMIT 1",
                    [$group, $code, $dbKey, GP247_STORE_ID_GLOBAL]
                );
                
                $shouldUpdate = true;
                if (!empty($existingResult) && $existingResult[0]->value === $value) {
                    $shouldUpdate = false; // Value hasn't changed
                }
                
                if ($shouldUpdate) {
                    // Use raw SQL to handle the upsert operation safely
                    DB::statement(
                        "INSERT INTO gp247_admin_config (`group`, code, `key`, store_id, value, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                         ON DUPLICATE KEY UPDATE code = VALUES(code), value = VALUES(value), updated_at = NOW()",
                        [$group, $code, $dbKey, GP247_STORE_ID_GLOBAL, $value]
                    );
                }
            }

            // Redirect back to settings with success message and stay on settings tab
            return redirect()
                ->route('admin.tropipay.settings')
                ->with('success', gp247_language_render($this->plugin->appPath.'::lang.configuration_updated'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.tropipay.settings')
                ->with('error', 'Error saving configuration: ' . $e->getMessage());
        }
    }

    public function testConnection()
    {
        // Handle both AJAX and regular POST requests
        if (request()->isMethod('post')) {
            try {
                $clientId = $this->getConfigValue('TropiPay_client_id', '');
                $clientSecret = $this->getConfigValue('TropiPay_client_secret', '');
                $sandbox = $this->getConfigValue('TropiPay_sandbox', '0');
                
                if (empty($clientId) || empty($clientSecret)) {
                    $errorMsg = 'Client ID y Client Secret son requeridos';
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMsg
                        ]);
                    } else {
                        return redirect()->route('admin.tropipay.settings')
                            ->with('error', $errorMsg);
                    }
                }

                // Use the TropiPayService to test connection properly
                $tropiPayService = new \App\GP247\Plugins\TropiPay\Services\TropiPayService();
                
                // Override the configuration for testing
                $tropiPayService->clientId = $clientId;
                $tropiPayService->clientSecret = $clientSecret;
                $tropiPayService->sandbox = ($sandbox == '1');
                $tropiPayService->baseUrl = $tropiPayService->sandbox
                    ? 'https://sandbox.tropipay.me/api/v3'
                    : 'https://www.tropipay.com/api/v3';

                // Test connection using the new simplified test method
                try {
                    $connectionResult = $tropiPayService->testConnection();
                    
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => true,
                            'connection_info' => $connectionResult,
                            'message' => $connectionResult['message'] ?? gp247_language_render($this->plugin->appPath.'::lang.connection_successful'),
                            'debug' => [
                                'endpoint' => $tropiPayService->baseUrl,
                                'sandbox' => $tropiPayService->sandbox,
                                'auth_success' => $connectionResult['authenticated'] ?? false
                            ]
                        ]);
                    } else {
                        return redirect()->route('admin.tropipay.settings')
                            ->with('success', $connectionResult['message'] ?? gp247_language_render($this->plugin->appPath.'::lang.connection_successful'));
                    }
                } catch (\Exception $authException) {
                    // Authentication failed, provide detailed error
                    $errorMsg = 'Error de autenticación con TropiPay: ' . $authException->getMessage();
                    
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMsg,
                            'debug' => [
                                'auth_error' => true,
                                'exception_class' => get_class($authException),
                                'code' => $authException->getCode(),
                                'file' => $authException->getFile(),
                                'line' => $authException->getLine(),
                                'endpoint' => $tropiPayService->baseUrl . '/access/token',
                                'sandbox' => $tropiPayService->sandbox,
                                'client_id_length' => strlen($clientId),
                                'client_secret_length' => strlen($clientSecret)
                            ]
                        ]);
                    } else {
                        return redirect()->route('admin.tropipay.settings')
                            ->with('error', $errorMsg);
                    }
                }
                
            } catch (\Exception $e) {
                $errorMsg = 'Error de conexión con TropiPay: ' . $e->getMessage();
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg,
                        'debug' => [
                            'exception_class' => get_class($e),
                            'code' => $e->getCode(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ]
                    ]);
                } else {
                    return redirect()->route('admin.tropipay.settings')
                        ->with('error', $errorMsg);
                }
            }
        }
        
        // Fallback for non-AJAX requests
        return redirect()->route('admin.tropipay.index');
    }

    public function orders()
    {
        // Get TropiPay orders with filtering
        $query = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay');
        
        // Apply filters
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        if (request('order_id')) {
            $query->where('id', request('order_id'));
        }
        
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $title = gp247_language_render($this->plugin->appPath.'::lang.orders_paid_with_tropipay');
        
        return view($this->plugin->appPath.'::admin.orders', [
            'title' => $title,
            'orders' => $orders,
            'plugin' => $this->plugin,
        ]);
    }

    /**
     * Get TropiPay order statistics for dashboard
     */
    private function getTropiPayOrderStats()
    {
        $totalOrders = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay')->count();
        $paidOrders = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay')
            ->where('payment_status', 3) // Paid status
            ->count();
        $pendingOrders = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay')
            ->whereNotIn('payment_status', [3, 4]) // All statuses except paid and failed
            ->count();
        $failedOrders = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay')
            ->where('payment_status', 4) // Failed status
            ->count();
            
        $totalRevenue = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay')
            ->where('payment_status', 3)
            ->sum('total');
            
        $todayRevenue = \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay')
            ->where('payment_status', 3)
            ->whereDate('created_at', today())
            ->sum('total');
        
        $successRate = $totalOrders > 0 ? round(($paidOrders / $totalOrders) * 100, 1) : 0;
        
        return [
            'total_orders' => $totalOrders,
            'paid_orders' => $paidOrders,
            'pending_orders' => $pendingOrders,
            'failed_orders' => $failedOrders,
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'success_rate' => $successRate,
        ];
    }

    /**
     * Get recent TropiPay orders for dashboard
     */
    private function getRecentTropiPayOrders()
    {
        return \GP247\Shop\Models\ShopOrder::where('payment_method', 'TropiPay')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Helper method to get configuration value from database
     */
    private function getConfigValue($key, $default = '')
    {
        // Convert key to match database format: TropiPay_client_id (mixed case)
        // Convert tropipay_default_currency -> TropiPay_default_currency
        $dbKey = preg_replace_callback('/^tropipay_/', function($matches) {
            return 'TropiPay_';
        }, $key);
        
        // Determine the correct group and code based on the key
        if (str_contains($dbKey, '_default_currency')) {
            // Currency configuration is stored in the Plugins group with TropiPay code
            $group = 'Plugins';
            $code = 'TropiPay';
        } else {
            // Other TropiPay configurations are stored in the TropiPay group
            $group = 'TropiPay';
            $code = 'TropiPay';
        }
        
        // Use raw SQL to avoid backtick issues with Eloquent
        $result = DB::select(
            "SELECT value FROM gp247_admin_config WHERE `group` = ? AND `code` = ? AND `key` = ? AND store_id = ? LIMIT 1",
            [$group, $code, $dbKey, GP247_STORE_ID_GLOBAL]
        );
        
        return !empty($result) ? $result[0]->value : $default;
    }
}
