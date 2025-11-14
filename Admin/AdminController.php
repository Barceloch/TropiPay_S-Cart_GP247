<?php
#App\GP247\Plugins\TropiPay\Admin\AdminController.php

namespace App\GP247\Plugins\TropiPay\Admin;

use GP247\Core\Controllers\RootAdminController;
use App\GP247\Plugins\TropiPay\AppConfig;

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
        return view($this->plugin->appPath.'::Admin',
            [
                
            ]
        );
    }

    public function save(\Illuminate\Http\Request $request)
{
    $data = $request->except(['_token']);
    
    foreach ($data as $key => $value) {
        \GP247\Core\Models\AdminConfig::updateOrCreate(
            ['code' => 'TropiPay', 'key' => $key],
            ['value' => $value, 'detail' => ucfirst(str_replace('_', ' ', $key))]
        );
    }
    
    return redirect()->back()->with('success', 'Configuration saved!');
}
}
