<?php

use Illuminate\Support\Facades\Route;
use App\GP247\Plugins\TropiPay\Controllers\TropiPayController;
use App\GP247\Plugins\TropiPay\Admin\TropiPayAdminController;

Route::group(['prefix' => 'plugin/tropipay', 'middleware' => ['web']], function () {
    Route::get('/start/{orderId}', [TropiPayController::class, 'startPayment'])->name('tropipay.start');
    Route::get('/return', [TropiPayController::class, 'handleReturn'])->name('tropipay.return');
    Route::post('/webhook', [TropiPayController::class, 'webhook'])->name('tropipay.webhook');
});

// Rutas de administración (ajusta el prefijo según tu configuración GP247_ADMIN_PREFIX)
Route::group(['prefix' => config('gp247.admin_prefix', 'gp247_admin') . '/tropipay', 'middleware' => ['web', 'gp247.admin']], function () {
    Route::get('/', [TropiPayAdminController::class, 'index'])->name('tropipay.admin.config');
    Route::post('/', [TropiPayAdminController::class, 'save'])->name('tropipay.admin.config.save');
});
