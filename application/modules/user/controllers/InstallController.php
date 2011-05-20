<?php

/**
 * 	SlyS
 *
 * @abstract   contains User_IndexController class, extending Zend_Controller_Action
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: IndexController.php 1018 2011-01-13 14:28:24Z deeper $
 */
namespace User;
/**
 * User authorization pages
 */
class InstallController extends \Zend\Controller\Action
{
    public function indexAction()
    {
        $options = $this->getInvokeArg('bootstrap')->getOption('user');
        \Zend\Debug::dump($options);
        if(!empty($options['installed'])) {
            throw new \Exception('Module already installed');
        }
        $form = new Form\Install\Admin();
        if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            
            $userModel = new Model\Users();
            $userModel->createDefaultUser(
                    $form->getValue('admin_name'),                     
                    $form->getValue('admin_password'),
                    $form->getValue('admin_role'),
                    $form->getValue('guest_role'));
            
            $modulesPlugin = $this->getInvokeArg('bootstrap')->getBroker()->load('modules');
            $modulesPlugin->installModule('user');
            
        }
        $this->view->initForm = $form;
    }
}