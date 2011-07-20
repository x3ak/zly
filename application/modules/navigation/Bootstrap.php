<?php

/**
 * Slys
 *
 * Navigation module bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1139 2011-01-28 16:07:30Z criolit $
 */

namespace Navigation;

use \Slys\Application\Module as Module, 
    \Slys\Api\Request as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap
                implements Module\Enableable
{
    protected function _initRegisterHelper()
    {
//        if (!$this->getApplication()->hasResource('view'))
//            $this->getApplication()->getBroker()->register('view', new \Zend\Application\Resource\View());
//
//        $this->getApplication()->bootstrap('view');
//
//        $this->getApplication()->getResource('view')->broker()->register(
//                'adminCurrentSubmenu',
//                new View\Helper\AdminCurrentSubmenu()
//        );
//
//        $this->getApplication()->getResource('view')->broker()->register(
//                'arrayTreeToTable',
//                new View\Helper\ArrayTreeToTable()
//        );
    }

    protected function _initPlugins()
    {
//        \Zend\Controller\Front::getInstance()->registerPlugin(new Plugin\Init());
    }
    
    public function enable()
    {
        return true;
    }
    
    public function disable()
    {
        return true;
    }
}