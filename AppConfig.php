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
        $return = ['error' => 0, 'msg' => 'Install successful!'];
        $this->copyPublicFiles();
        $this->createConfigs();
        \Illuminate\Support\Facades\Cache::forget('gp247_payment_methods');
        return $return;
    }

    public function uninstall()
    {
        $return = ['error' => 0, 'msg' => 'Uninstall successful!'];
        AdminConfig::where('code', 'TropiPay')->delete();
        \Illuminate\Support\Facades\Cache::forget('gp247_payment_methods');
        File::deleteDirectory(public_path('GP247/Plugins/TropiPay'));
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
        $configs = [
            ['code' => 'TropiPay', 'key' => 'client_id', 'value' => '', 'sort' => 1, 'detail' => 'Client ID', 'store_id' => $storeId],
            ['code' => 'TropiPay', 'key' => 'client_secret', 'value' => '', 'sort' => 2, 'detail' => 'Client Secret', 'store_id' => $storeId],
            ['code' => 'TropiPay', 'key' => 'server_mode', 'value' => 'Development', 'sort' => 3, 'detail' => 'Server Mode', 'store_id' => $storeId],
            ['code' => 'TropiPay', 'key' => 'currency', 'value' => 'EUR', 'sort' => 4, 'detail' => 'Currency', 'store_id' => $storeId],
            ['code' => 'TropiPay', 'key' => 'status', 'value' => '1', 'sort' => 5, 'detail' => 'Status', 'store_id' => $storeId],
        ];

        foreach ($configs as $config) {
            AdminConfig::updateOrCreate(
                ['code' => $config['code'], 'key' => $config['key'], 'store_id' => $config['store_id']],
                $config
            );
        }
    }
}
