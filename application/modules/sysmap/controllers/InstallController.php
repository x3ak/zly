<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: IndexController.php 1018 2011-01-13 14:28:24Z deeper $
 */
namespace Sysmap;
/**
 * Sysmap installer controller
 */
class InstallController extends \Zend\Controller\Action
{
    public function indexAction()
    {
        $options = $this->getInvokeArg('bootstrap')->getOption('sysmap');

        if(!empty($options['installed'])) {
            throw new \Exception('Module already installed');
        }
        $mapModel = new Model\Map();
        $mapModel->initSchema();
        $modulesPlugin = $this->getInvokeArg('bootstrap')->getBroker()->load('modules');
        $modulesPlugin->installModule('sysmap');
//        $this->broker('redirector')->gotoUrl($this->view->broker('url')->direct(
//                array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true));
    }
}