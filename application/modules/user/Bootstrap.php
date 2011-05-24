<?php

/**
 * Slys
 *
 * Users authentification and ACL support for the Slys applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */
namespace User;

use \Slys\Application\Module as Module, 
    \Slys\Api\Request        as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap 
                implements Api\Requestable, Module\Installable, Module\Updateable, Module\Enableable
{
    
    protected function _initAcl()
    {
        $this->bootstrap('Frontcontroller');
        $this->getResource('Frontcontroller')->registerPlugin(
                new \User\Plugin\Acl(new \Zend\Acl\Acl())
        );
    }

    public function onRequest(Api\Request $request)
    {
        switch ($request->getName()) {
            case 'navigation.get-module-navigation':
                $types = $this->getResourceLoader()->getResourceTypes();
                $navigationPath = $types['config']['path'].DIRECTORY_SEPARATOR.'navigation.yml';
                if(is_file($navigationPath)) {
                    $navigation = new \Zend\Navigation\Page\Mvc(new \Zend\Config\Yaml($navigationPath));
                    $navName = $navigation->getLabel();
                    if(!empty($navName))
                        $request->getResponse()->setData($navigation);
                }
            break;
        }
    }
    
    public function install()
    {
        return new \Zend\Controller\Request\Simple('index','install','user');
    }
    
    public function uninstall()
    {
        return true;
    }
    
    public function update()
    {
        return 'User updated';
    }
    
    
    public function enable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('user');
        return true;
    }
    
    public function disable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('user', false);
        return true;
    }

}
