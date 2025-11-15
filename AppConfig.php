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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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
            ->where('code', $this->configCode)
            ->first();
            
        if ($check) {
            return ['error' => 1, 'msg' => gp247_language_render('admin.extension.plugin_exist')];
        }
        
        try {
            // Insertar el registro principal del plugin
            $dataInsert = [
                [
                    'group' => $this->configGroup,
                    'code' => $this->configCode,  // "Payment"
                    'key' => $this->configKey,     // "TropiPay"
                    'sort' => 0,
                    'store_id' => GP247_STORE_ID_GLOBAL,
                    'value' => self::ON,
                    'detail' => $this->appPath.'::lang.title',
                ],
            ];
            
            AdminConfig::insert($dataInsert);
            
            // Crear configuraciones adicionales
            $this->createConfigs();
            
            // Copiar archivos públicos
            $this->copyPublicFiles();
            
            (new ExtensionModel)->installExtension();
            
            return [
                'error' => 0, 
                'msg' => gp247_language_render('admin.extension.install_success'),
                'key' => $this->configKey
            ];
            
        } catch (\Throwable $e) {
            return ['error' => 1, 'msg' => $e->getMessage()];
        }
    }

    public function uninstall()
    {
        try {
            AdminConfig::where('code', 'TropiPay')->delete();
            \Illuminate\Support\Facades\Cache::forget('gp247_payment_methods');
            if (File::isDirectory(public_path('GP247/Plugins/TropiPay'))) {
                File::deleteDirectory(public_path('GP247/Plugins/TropiPay'));
            }
            return ['error' => 0, 'msg' => 'Uninstall successful!'];
        } catch (\Exception $e) {
            Log::error('TropiPay Uninstall Error: ' . $e->getMessage());
            return ['error' => 1, 'msg' => 'Uninstall failed: ' . $e->getMessage()];
        }
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
        }

        //Admin config home
        AdminHome::where('extension', $this->appPath)->update(['status' => 0]);

        return $return;
    }

    public function setupStore($storeId = null)
    {
        $this->createConfigs($storeId);
        return ['error' => 0, 'msg' => 'Setup store successful!'];
    }

    public function removeStore($storeId = null)
    {
        AdminConfig::where('code', 'TropiPay')->where('store_id', $storeId)->delete();
        return ['error' => 0, 'msg' => 'Remove store successful!'];
    }

    // Process when click button plugin in admin    
    public function clickApp()
    {
        //
    }

    /**
     * Get info plugin
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
            'value' => 0,
            'appPath' => $this->appPath
        ];

        return $arrData;
    }

    /**
     * Copiar archivos públicos
     */
    private function copyPublicFiles()
    {
        $source = __DIR__ . '/public';
        $destination = public_path('GP247/Plugins/TropiPay');
        
        if (File::isDirectory($source)) {
            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            File::copyDirectory($source, $destination);
        }
    }

    /**
     * Crear configuraciones en la base de datos
     */
    private function createConfigs($storeId = null)
    {
        $storeId = $storeId ?? GP247_STORE_ID_GLOBAL;
        
        $configs = [
            [
                'group' => $this->configGroup,
                'code' => $this->configCode,  // "Payment"
                'key' => $this->configKey . '_client_id',  // "TropiPay_client_id"
                'value' => '',
                'sort' => 1,
                'detail' => 'TropiPay Client ID',
                'store_id' => $storeId
            ],
            [
                'group' => $this->configGroup,
                'code' => $this->configCode,
                'key' => $this->configKey . '_client_secret',
                'value' => '',
                'sort' => 2,
                'detail' => 'TropiPay Client Secret',
                'store_id' => $storeId
            ],
            [
                'group' => $this->configGroup,
                'code' => $this->configCode,
                'key' => $this->configKey . '_server_mode',
                'value' => 'Development',
                'sort' => 3,
                'detail' => 'Server Mode (Development/Production)',
                'store_id' => $storeId
            ],
            [
                'group' => $this->configGroup,
                'code' => $this->configCode,
                'key' => $this->configKey . '_status',
                'value' => '1',
                'sort' => 4,
                'detail' => 'Status (1: Active, 0: Inactive)',
                'store_id' => $storeId
            ],
        ];

        foreach ($configs as $config) {
            AdminConfig::updateOrCreate(
                [
                    'code' => $config['code'], 
                    'key' => $config['key'], 
                    'store_id' => $config['store_id']
                ],
                $config
            );
        }
    }

    
}