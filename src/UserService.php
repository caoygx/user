<?php
namespace mapp;

use Casbin\Enforcer;
use Casbin\Model\Model;
use Casbin\Log\Log;
use think\Service;
use mapp\command\Publish;

/**
 * mapp service
 * 
 * @author techlee@qq.com
 */
class UserService extends Service
{
    /**
     * Register service
     *
     * @return void
     */
    public function register()
    {
        // 注册数据迁移服务
        $this->app->register(\think\migration\Service::class);
        
        // 绑定 Casbin决策器
        $this->app->bind('enforcer', function () {
            $default = $this->app->config->get("user.default");
            
            $config = $this->app->config->get("user.enforcers.".$default);
            $adapter = $config['adapter'];
            
            $configType = $config['model']['config_type'];
            
            $model = new Model();
            if ('file' == $configType) {
                $model->loadModel($config['model']['config_file_path']);
            } elseif ('text' == $configType) {
                $model->loadModel($config['model']['config_text']);
            }
            
            return new Enforcer($model, app($adapter), $this->app->config->get("user.log.enabled", false));
        });
    }

    /**
     * Boot function
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/user.php', 'user');



        $this->commands(['user:publish' => Publish::class]);
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom(string $path, string $key)
    {
        $config = $this->app->config->get($key, []);

        $this->app->config->set(array_merge(require $path, $config), $key);
    }
}