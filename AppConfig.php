<?php
/**
 * Plugin format 1.0
 */
#App\GP247\Plugins\TropiPay\AppConfig.php
namespace App\GP247\Plugins\TropiPay;

use App\GP247\Plugins\TropiPay\Models\ExtensionModel;
use GP247\Core\Models\AdminConfig;
use GP247\Core\Models\AdminHome;
use GP247\Core\ExtensionConfigDefault;
use GP247\Core\Models\AdminMenu;
use Illuminate\Support\Facades\DB;
class AppConfig extends ExtensionConfigDefault
{
    public function __construct()
    {
        //Read config from gp247.json
        $config = file_get_contents(__DIR__.'/gp247.json');
        $config = json_decode($config, true);
    	$this->configGroup = $config['configGroup'];
        $this->configKey = $config['configKey'];
        $this->configCode = $config['configCode'] ?? $this->configKey;
        $this->requireCore = $config['requireCore'] ?? [];
        $this->requirePackages = $config['requirePackages'] ?? [];
        $this->requireExtensions = $config['requireExtensions'] ?? [];
        //Path
        $this->appPath = $this->configGroup . '/' . $this->configKey;
        //Language
        $this->title = trans($this->appPath.'::lang.title');
        //Image logo or thumb
        $this->image = $this->appPath.'/'.$config['image'];
        //
        $this->version = $config['version'];
        $this->auth = $config['auth'];
        $this->link = $config['link'];
    }

    public function install()
    {
        $check = AdminConfig::where('key', $this->configKey)
            ->where('group', $this->configGroup)->first();
        if ($check) {
            //Check Plugin key exist
            $return = ['error' => 1, 'msg' =>  gp247_language_render('admin.extension.plugin_exist')];
        } else {
            // Insert plugin to config
            $dataInsert = [
                // Plugin enable/disable
                [
                    'group'  => $this->configGroup,
                    'code'    => $this->configCode,
                    'key'    => $this->configKey,
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => self::ON, //Enable extension
                    'detail' => $this->appPath.'::lang.title',
                ],
                // Core plugin settings (in TropiPay group)
                [
                    'group'  => 'TropiPay',
                    'code'    => 'TropiPay',
                    'key'    => 'TropiPay_client_id',
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => '', //Empty by default
                    'detail' => $this->appPath.'::lang.client_id',
                ],
                [
                    'group'  => 'TropiPay',
                    'code'    => 'TropiPay',
                    'key'    => 'TropiPay_client_secret',
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => '', //Empty by default
                    'detail' => $this->appPath.'::lang.client_secret',
                ],
                [
                    'group'  => 'TropiPay',
                    'code'    => 'TropiPay',
                    'key'    => 'TropiPay_sandbox',
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => '0', //Production by default
                    'detail' => $this->appPath.'::lang.sandbox_mode',
                ],
                [
                    'group'  => 'TropiPay',
                    'code'    => 'TropiPay',
                    'key'    => 'TropiPay_enabled',
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => '0', //Disabled by default
                    'detail' => $this->appPath.'::lang.enabled',
                ],
                // Plugin configuration (in Plugins group)
                [
                    'group'  => 'Plugins',
                    'code'    => $this->configKey.'_config',
                    'key'    => $this->configKey.'_order_status_success',
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => 2, //Order status processing
                    'detail' => $this->appPath.'::lang.order_status_success',
                ],
                [
                    'group'  => 'Plugins',
                    'code'    => $this->configKey.'_config',
                    'key'    => $this->configKey.'_payment_status_success',
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => 3, //Order payment paid
                    'detail' => $this->appPath.'::lang.payment_status_success',
                ],
                [
                    'group'  => 'Plugins',
                    'code'    => $this->configKey.'_config',
                    'key'    => $this->configKey.'_default_currency',
                    'sort'   => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value'  => 'USD', //Default currency USD
                    'detail' => $this->appPath.'::lang.default_currency',
                ],
            ];
            try {
                AdminConfig::insertOrIgnore(
                    $dataInsert
                );
                
                // Admin config home - Register plugin in admin menu
                AdminHome::updateOrCreate(
                    ['extension' => $this->appPath],
                    [
                        'view' => 'tropipay::admin.dashboard',
                        'size' => 12,
                        'status' => 1,
                        'sort' => 0,
                    ]
                );
                
                (new ExtensionModel)->installExtension();
                
                // Insert menu - Register plugin in admin payment menu
                $checkIdBlockPayment = AdminMenu::where('key', 'ADMIN_SHOP_PAYMENT')->first();
                if ($checkIdBlockPayment) {
                    $menu = [
                        [
                            'parent_id' => $checkIdBlockPayment->id,
                            'sort' => 30,
                            'title' => 'TropiPay',
                            'icon' => 'fas fa-credit-card',
                            'uri' => 'admin::tropipay',
                            'key' => null,
                            'type' => 0
                        ]
                    ];
                    AdminMenu::insertOrIgnore($menu);
                }
                
                $return = ['error' => 0, 'msg' => gp247_language_render('admin.extension.install_success')];
            } catch (\Throwable $e) {
                $return = ['error' => 1, 'msg' => $e->getMessage()];
            }
        }

        return $return;
    }

    public function uninstall()
    {
        //Please delete all values inserted in the installation step
        try {
            // Delete plugin main configuration
            AdminConfig::where('key', $this->configKey)
                ->delete();
            
            // Delete TropiPay group configurations
            AdminConfig::where('group', 'TropiPay')
                ->where('code', 'TropiPay')
                ->delete();
            
            // Delete plugin configuration settings
            AdminConfig::where('code', $this->configKey.'_config')
                ->delete();

            //Admin config home
            AdminHome::where('extension', $this->appPath)->delete();
            AdminHome::where('extension', 'Plugins/TropiPay')->delete();
            AdminHome::where('extension', 'gp247-tropipay::Admin')->delete();
            AdminHome::where('extension', 'tropipay')->delete();

            //Delete menu
            AdminMenu::where('uri', 'admin::tropipay')->delete();

            (new ExtensionModel)->uninstallExtension();

            $return = ['error' => 0, 'msg' => gp247_language_render('admin.extension.uninstall_success')];
        } catch (\Throwable $e) {
            $return = ['error' => 1, 'msg' => $e->getMessage()];
        }

        return $return;
    }
    
    public function enable()
    {
        $process = (new AdminConfig)
            ->where('group', $this->configGroup)
            ->where('key', $this->configKey)
            ->update(['value' => self::ON]);
        //Admin config home
        AdminHome::where('extension', $this->appPath)->update(['status' => 1]);

        if (!$process) {
            $return = ['error' => 1, 'msg' => gp247_language_render('admin.extension.action_error', ['action' => 'Enable'])];
        }
        $return = ['error' => 0, 'msg' => gp247_language_render('admin.extension.enable_success')];
        return $return;
    }

    public function disable()
    {
        $return = ['error' => 0, 'msg' => ''];
        $process = (new AdminConfig)
            ->where('group', $this->configGroup)
            ->where('key', $this->configKey)
            ->update(['value' => self::OFF]);
        if (!$process) {
            $return = ['error' => 1, 'msg' => 'Error disable'];
        } else {
            //Admin config home
            AdminHome::where('extension', $this->appPath)->update(['status' => 0]);
        }

        return $return;
    }


    // Remove setup for store

    public function removeStore($storeId = null)
    {
        // code here
    }

    // Setup for store

    public function setupStore($storeId = null)
    {
       // code here
    }


    // Process when click button plugin in admin    
    
    public function clickApp()
    {
        return redirect(gp247_route_admin('admin.tropipay.index'));
    }

    /**
     * Get info plugin
     *
     * @return  [type]  [return description]
     */
    public function getInfo()
    {
        $arrData = [
            'title' => $this->title,
            'key' => $this->configKey,
            'code' => $this->configCode,
            'image' => $this->image,
            'permission' => self::ALLOW,
            'version' => $this->version,
            'auth' => $this->auth,
            'link' => $this->link,
            'value' => 0, // this return need for plugin shipping
            'appPath' => $this->appPath
        ];

        return $arrData;
    }
}

