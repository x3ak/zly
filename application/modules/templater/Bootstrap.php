<?php
/**
 * Zly
 *
 * Theme support for the Zly applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */

namespace Templater;

use \Zly\Application\Module as Module, 
    \Zly\Api\Request as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap 
                implements Module\Installable, 
                           Module\Updateable, 
                           Module\Enableable
{
    
    /**
     * Tempalter plugins initialization
     */
    protected function _initPlugins()
    {
        $plugin = new Plugin\Layout($this->getOptions());
        
        \Zend\Controller\Front::getInstance()
            ->registerPlugin($plugin);
    }

    public function onRequest(Request $request)
    {
        switch ($request->getName()) {
            case 'navigation.get-module-navigation':
                $types = $this->getResourceLoader()->getResourceTypes();
                $navigationPath = $types['config']['path'].DIRECTORY_SEPARATOR.'navigation.yml';
                if(is_file($navigationPath)) {
                    $navigation = new Zend_Navigation_Page_Mvc(new Zend_Config_Yaml($navigationPath));
                    $request->getResponse()->setData($navigation);
                }
            break;
        }
    }
    
    public function enable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('templater');
        return true;
    }
    
    public function disable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('templater', false);
        return true;
    }
    
    public function install()
    {
        $options = $this->getOptions();

        if(!empty($options['installed'])) {
            throw new \Exception('Module already installed');
        }
        $mapModel = new Model\Themes();
        $mapModel->initSchema();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('templater');
        return true;
    }
    
    public function update()
    {
        $themeModule = new Model\Themes();
        $themeModule->updateSchema();
        return true;
    }
    
    public function uninstall()
    {
        $options = $this->getOptions();

        if(empty($options['installed'])) {
            throw new \Exception('Module not installed');
        }
        $mapModel = new Model\Themes();
        $mapModel->dropSchema();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('templater', false);
        $this->disable();
        return true;
    }

}
