<?php
namespace Page;

use \Zly\Application\Module as Module, 
    \Zly\Api as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap 
                implements Module\Installable, Module\Updateable
{

    public function _initRoutes()
    {
        \Zend\Controller\Front::getInstance()->getRouter()->addRoute('page',
            new \Zend\Controller\Router\Route\Regex(
                'page/([a-zA-Z0-9\-_]*)\.html',
                array(
                    'module' => 'page',
                    'controller' => 'index',
                    'action' => 'view'
                ),
                array(
                    1 => 'pagename'
                ),
                'page/%s.html'
            ));
    }
    
    public function install()
    {
        $options = $this->getOptions();

        if(!empty($options['installed'])) {
            throw new \Exception('Module already installed');
        }
        $mapModel = new Model\Pages();
        $mapModel->initSchema();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('page');
        return true;
    }
    
    public function uninstall() 
    {
        $options = $this->getOptions();

        if(empty($options['installed'])) {
            throw new \Exception('Module not installed');
        }
        $mapModel = new Model\Pages();
        $mapModel->dropSchema();
        $modulesPlugin = $this->getBroker()->load('modules');
        $modulesPlugin->installModule('page', false);
        return true;
    }
    
    public function update() 
    {
        $mapModel = new Model\Pages();
        $mapModel->updateSchema();
        return true;
    }
}