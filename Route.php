<?php
use Illuminate\Support\Facades\Route;

$config = file_get_contents(__DIR__.'/gp247.json');
$config = json_decode($config, true);

if(gp247_extension_check_active($config['configGroup'], $config['configKey'])) {


    Route::group(
    [
        'middleware' => GP247_FRONT_MIDDLEWARE,
        'prefix'    => 'plugin/tropipay',
        'namespace' => 'App\GP247\Plugins\TropiPay\Controllers',
    ],
    function () {
        Route::get('index', 'FrontController@index')
        ->name('tropipay.index');
    }
);

    Route::group(
        [
            'prefix' => GP247_ADMIN_PREFIX.'/tropipay',
            'middleware' => GP247_ADMIN_MIDDLEWARE,
            'namespace' => '\App\GP247\Plugins\TropiPay\Admin',
        ], 
        function () {
            Route::get('/', 'AdminController@index')
            ->name('admin_tropipay.index');
        }
    );
}