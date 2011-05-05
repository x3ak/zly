<?php
/**
 * Slys
 *
 * Theme support for the Slys applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */

namespace Templater;

use \Slys\Application\Module as Module, 
    \Slys\Api\Request as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap implements Api\Requestable, Module\Installable, Module\Updateable
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

    public function onRequest(Api\Request $request)
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
    
    public function install()
    {
        return 'Templater installed';
    }
    
    public function update()
    {
        return 'Templater updated';
    }
    
    public function uninstall()
    {
        return 'Templater uninstalled';
    }

}