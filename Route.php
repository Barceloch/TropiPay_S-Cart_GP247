<?php

use Illuminate\Support\Facades\Route;

$config = file_get_contents(__DIR__.'/gp247.json');
$config = json_decode($config, true);

// Always load admin routes for configuration
Route::group([
    'prefix' => GP247_ADMIN_PREFIX . '/tropipay',
    'middleware' => GP247_ADMIN_MIDDLEWARE,
    'namespace' => 'App\GP247\Plugins\TropiPay\Admin',
], function () {
    Route::get('/', 'AdminController@index')->name('admin.tropipay.index');
    Route::get('settings', 'AdminController@settings')->name('admin.tropipay.settings');
    Route::post('settings', 'AdminController@updateSettings')->name('admin.tropipay.update_settings');
    Route::post('test-connection', 'AdminController@testConnection')->name('admin.tropipay.test_connection');
    Route::get('orders', 'AdminController@orders')->name('admin.tropipay.orders');
});

// Only load payment routes and webhooks when plugin is active
if(gp247_extension_check_active($config['configGroup'], $config['configKey'])) {
    // Public webhook route (no middleware, no prefix)
    Route::post('/tropipay/webhook', [
        'uses' => 'App\GP247\Plugins\TropiPay\Controllers\FrontController@webhook',
        'as' => 'tropipay.webhook'
    ]);

    // Front routes with proper middleware for TropiPay payment processing
    Route::group([
        'middleware' => GP247_FRONT_MIDDLEWARE,
        'namespace' => 'App\GP247\Plugins\TropiPay\Controllers',
    ], function () {
        // Success and error routes (called by TropiPay)
        Route::get('tropipay/success/{order_id}', 'FrontController@success')->name('tropipay.success');
        Route::get('tropipay/error/{order_id}', 'FrontController@error')->name('tropipay.error');
        
        // AJAX helper routes (optional)
        Route::get('plugin/tropipay/status', 'FrontController@getStatus')->name('tropipay.status');
    });
}