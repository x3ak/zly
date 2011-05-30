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

class IndexController extends \Zend\Controller\Action
{

    public function indexAction()
    {
        $this->_forward('index', 'profile');
    }

    /**
     * Display user box
     * @paramsform User_Form_Widget_UserBox
     */
    public function userBoxAction()
    {
        $this->view->boxType = $this->getRequest()->getParam('box_type');
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->loginAction();
            $this->render('login');
        } else {
            $this->view->userIdentity = Zend_Auth::getInstance()->getIdentity();
        }
    }

    /**
     * Login action
     */
    public function loginAction()
    {
        $form = new User_Form_Login();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // collect the data from the user
            $loginUsername = $form->getValue('login');
            $loginPassword = md5($form->getValue('password'));
            $slysAuth = Zend_Auth::getInstance();
            // do the authentication

            $authAdapter = $this->_getAuthAdapter($loginUsername, $loginPassword);
            $result = $slysAuth->authenticate($authAdapter);
            if (!$result->isValid()) {
                $form->setDecorators(array('Errors', 'FormElements', 'Form'));
                $form->addError('Wrong combination of username and password');
            } else {
                $identity = $authAdapter->getResultRowObject(null, 'password');
                $identity = User_Model_DbTable_User::getInstance()->getUser($identity->id);
                $slysAuth->getStorage()->write($identity);
                $this->_helper->getHelper('FlashMessenger')->addMessage('You are successful logged!');
                $this->_redirect($this->getRequest()->getRequestUri());
            }
            $this->view->loginForm = $form;
        } else {
            $this->view->loginForm = $form;
        }
       
    }

    /**
     * Logout action
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }

    /**
     * Return doctrine based auth adapter
     * @param string $username
     * @param string $password
     * @return ZendX_Doctrine_Auth_Adapter 
     */
    protected function _getAuthAdapter($username, $password)
    {
        $authAdapter = new ZendX_Doctrine_Auth_Adapter(Doctrine_Manager::getInstance()->getCurrentConnection());
        $authAdapter->setTableName('User_Model_Mapper_User u')
                ->setIdentityColumn('u.login')
                ->setCredentialColumn('u.password')
                ->setIdentity($username)
                ->setCredential($password);

        return $authAdapter;
    }
}