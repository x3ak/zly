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
                implements Module\Installable, Module\Updateable, Module\Enableable
{
    protected function _initAcl()
    {
        $this->bootstrap('Frontcontroller');
        $this->getResource('Frontcontroller')->registerPlugin(
                new \User\Plugin\Acl(new \Zend\Acl\Acl())
        );
    }

    public function install()
    {
        return new \Zend\Controller\Request\Simple('index','install','user');
    }
    
    public function uninstall()
    {
        $options = $this->getOptions();

        if(empty($options['installed'])) {
            throw new \Exception('Module not installed');
        }
        $mapModel = new Model\Users();
        $mapModel->dropSchema();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('user', false);
        return true;
    }
    
    public function update()
    {
        $mapModel = new Model\Users();
        $mapModel->updateSchema();
        return true;
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
