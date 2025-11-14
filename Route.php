<?php
use Illuminate\Support\Facades\Route;
use App\GP247\Plugins\TropiPay\Controllers\PaymentController;

$config = file_get_contents(__DIR__.'/gp247.json');
$config = json_decode($config, true);

if(gp247_extension_check_active($config['configGroup'], $config['configKey'])) {

    // Rutas de pago (CRÍTICAS)
    Route::group(['prefix' => 'tropipay', 'middleware' => ['web']], function () {
        Route::post('process', [PaymentController::class, 'process'])->name('tropipay.process');
        Route::get('success', [PaymentController::class, 'success'])->name('tropipay.success');
        Route::get('failed', [PaymentController::class, 'failed'])->name('tropipay.failed');
        Route::post('webhook', [PaymentController::class, 'webhook'])->name('tropipay.webhook');
    });

    // Ruta frontend (opcional)
    Route::group([
        'middleware' => GP247_FRONT_MIDDLEWARE,
        'prefix'    => 'plugin/tropipay',
        'namespace' => 'App\GP247\Plugins\TropiPay\Controllers',
    ], function () {
        //Route::get('index', 'FrontController@index')->name('tropipay.index');
    });

    // Rutas admin
    Route::group([
        'prefix' => GP247_ADMIN_PREFIX.'/tropipay',
        'middleware' => GP247_ADMIN_MIDDLEWARE,
        'namespace' => '\App\GP247\Plugins\TropiPay\Admin',
    ], function () {
        Route::get('/', 'AdminController@index')->name('admin_tropipay.index');
        Route::post('save', 'AdminController@save')->name('admin_tropipay.save');
    });
}