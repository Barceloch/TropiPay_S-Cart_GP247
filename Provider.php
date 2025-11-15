<?php

namespace App\GP247\Plugins\TropiPay;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Route.php');
        $this->loadViewsFrom(__DIR__ . '/Views', 'tropipay');
        $this->loadTranslationsFrom(__DIR__ . '/Lang', 'tropipay');
    }
}
