<?php

/**
 * 	SlyS
 *
 * @abstract   contains User_ProfileController class, extending Zend_Controller_Action
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: ProfileController.php 1018 2011-01-13 14:28:24Z deeper $
 */

/**
 * User profile pages
 */
class User_ProfileController extends Zend_Controller_Action
{
    /**
     * Display&Edit user profile form
     */
    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $userId = $identity->id;
        $userModel = new User_Model_Users();
        $user = $userModel->getUser($userId);
        $form = new User_Form_Profile();
        $form->populate($user->toArray());
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $result = $userModel->saveProfile($user, $form->getValues());
            if ($result) {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Your profile saved.');
                $this->_helper->redirector->gotoUrlAndExit($this->getRequest()->getRequestUri());
            }
        }
        $this->view->profile = $form;
    }

    /**
     * Change user password page
     */
    public function changePasswordAction()
    {
        $form = new User_Form_Password();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $userModel = new User_Model_Users();
            $identity = Zend_Auth::getInstance()->getIdentity();
            $userId = $identity->id;
            $user = $userModel->getUser($userId);
            $result = $userModel->savePassword($user, $form->getValue('new_password'), $form->getValue('password'));

            if ($result) {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Your password was changed.');                
            } else {
                $this->_helper->getHelper('FlashMessenger')->addMessage('Your password was NOT changed.');
            }
            $this->_redirect($this->getRequest()->getRequestUri());
        }
        $this->view->passwordForm = $form;
    }
}