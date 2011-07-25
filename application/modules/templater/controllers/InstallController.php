<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: IndexController.php 1018 2011-01-13 14:28:24Z deeper $
 */
namespace Templater;
/**
 * Sysmap installer controller
 */
class InstallController extends \Zend\Controller\Action
{
    public function indexAction()
    {
        $options = $this->getInvokeArg('bootstrap')->getOption('templater');

        if(!empty($options['installed'])) {
            throw new \Exception('Module already installed');
        }
        $mapModel = new Model\Theme();
        $mapModel->initSchema();
        $modulesPlugin = $this->getInvokeArg('bootstrap')->getBroker()->load('modules');
        $modulesPlugin->installModule('templater');
        $this->broker('redirector')->gotoUrl($this->view->broker('url')->direct(
                array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true));
    }
}