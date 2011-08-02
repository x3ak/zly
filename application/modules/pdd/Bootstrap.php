<?php
/**
 * Zly
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * 
 */

namespace Pdd;

use \Zly\Application\Module as Module, 
    \Zly\Api as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap 
                implements Module\Installable, Module\Updateable
{
    public function install()
    {
        $options = $this->getOptions();

        if(!empty($options['installed'])) {
            throw new \Exception('Module already installed');
        }
        $model = new Model\Cards();
        $model->initSchema();

        if(!mkdir($options['upload_directory'], 0777, true)) {
            throw new \Exception("Can't create upload directory: {$options['upload_directory']}");
        }
        
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('pdd');
        return true;
    }
    
    public function uninstall() 
    {
        $options = $this->getOptions();

        if(empty($options['installed'])) {
            throw new \Exception('Module not installed');
        }
        $model = new Model\Cards();
        $model->dropSchema();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('pdd', false);
        return true;
    }
    
    public function update() 
    {
        $model = new Model\Cards();
        $model->updateSchema();
        return true;
    }

}