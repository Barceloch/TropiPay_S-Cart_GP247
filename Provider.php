<?php

#App\GP247\Plugins\TropiPay\Provider.php
namespace App\GP247\Plugins\TropiPay;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/Views', 'Plugins/TropiPay');
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'Plugins/TropiPay');
    }
}