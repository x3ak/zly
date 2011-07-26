<?php

/**
 * Zly
 *
 * Navigation module bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1139 2011-01-28 16:07:30Z criolit $
 */

namespace Navigation;

use \Zly\Application\Module as Module, 
    \Zly\Api\Request as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap
                implements Module\Enableable, Module\Installable
{
    protected function _initRegisterHelper()
    {
        if (!$this->hasResource('view'))
            $this->getBroker()->register('view', new \Zend\Application\Resource\View());

        $this->getApplication()->bootstrap('view');

        $this->getApplication()->getResource('view')->broker()->register(
                'adminCurrentSubmenu',
                new View\Helper\AdminCurrentSubmenu()
        );

        $this->getApplication()->getResource('view')->broker()->register(
                'arrayTreeToTable',
                new View\Helper\ArrayTreeToTable()
        );
    }

    protected function _initPlugins()
    {
        
        \Zend\Controller\Front::getInstance()->registerPlugin(new Plugin\Init());
    }
    
    public function enable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('navigation');
        return true;
    }
    
    public function disable()
    {
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->enableModule('navigation', false);
        return true;
    }
    
    public function install()
    {
        $options = $this->getOptions();

        if(!empty($options['installed'])) {
            throw new \Exception('Module already installed');
        }
        $mapModel = new Model\Navigation();
        $mapModel->initSchema();
        $mapModel->createRoot();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('navigation');
        return true;
    }
    
    public function update()
    {
        $themeModule = new Model\Navigation();
        $themeModule->updateSchema();
        return true;
    }
    
    public function uninstall()
    {
        $options = $this->getOptions();

        if(empty($options['installed'])) {
            throw new \Exception('Module not installed');
        }
        $mapModel = new Model\Navigation();
        $mapModel->dropSchema();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('navigation', false);
        $this->disable();
        return true;
    }
}