<?php

namespace user\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * 发布配置文件、迁移文件指令
 */
class Publish extends Command
{
    /**
     * 配置指令
     */
    protected function configure()
    {
        $this->setName('user:publish')->setDescription('Publish User');
    }

    /**
     * 执行指令
     * @param Input  $input
     * @param Output $output
     * @return null|int
     * @throws LogicException
     * @see setCode()
     */
    protected function execute(Input $input, Output $output)
    {
        $destination = $this->app->getRootPath() . '/database/migrations/';
        if(!is_dir($destination)){
            mkdir($destination, 0755, true);
        }
        $source = __DIR__.'/../../database/migrations/';
        $handle = dir($source);
        
        while($entry=$handle->read()) {   
            if(($entry!=".")&&($entry!="..")){
                if(is_file($source.$entry)){
                    copy($source.$entry, $destination.$entry);   
                }
            }
        }

        if (!file_exists(config_path().'user.php')) {
            copy(__DIR__.'/../../config/user.php', config_path().'user.php');
        }
    }
}

