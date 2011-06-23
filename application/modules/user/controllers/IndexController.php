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

    /**
     * Authentification service
     * @var \Zend\Authentication\AuthenticationService 
     */
    protected $_auth;
    
    /**
     * Controller resorces initialization
     */
    public function init()
    {
        $this->_auth = $this->getFrontController()->getPlugin('User\Plugin\Acl')->getAuthentificationService();
    }
    
    /**
     * Short page for user profile
     */
    public function indexAction()
    {
        $this->_forward('index', 'profile');
    }

    /**
     * Display user box
     * @Qualifier \User\Form\Widget\UserBox
     */
    public function userBoxAction()
    {
        $this->view->boxType = $this->getRequest()->getParam('box_type');
        if (!$this->_auth->hasIdentity()) {
            $this->loginAction();
            $this->render('login');
        } else {
            $this->view->userIdentity = $this->_auth->getIdentity();
        }
    }

    /**
     * User login action
     */
    public function loginAction()
    {
        $form = new Form\Login();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // collect the data from the user
            $loginUsername = $form->getValue('login');
            $loginPassword = md5($form->getValue('password'));

            // do the authentication

            $authAdapter = $this->_getAuthAdapter($loginUsername, $loginPassword);
            $result = $this->_auth->authenticate($authAdapter);
            if (!$result->isValid()) {
                $form->setDecorators(array('Errors', 'FormElements', 'FormDecorator'));
                $form->addError('Wrong combination of username and password');
            } else {
                $userModel = new Model\Users();
                $identity = $authAdapter->getResultRowObject(null, 'password');
                $identity = $userModel->getUser($identity->getId());
                $this->_auth->getStorage()->write($identity);

                $this->broker('FlashMessenger')->addMessage('You are successful logged!');
                $this->broker('redirector')->gotoUrl($this->getRequest()->getRequestUri());
            }
            $this->view->loginForm = $form;
        } else {
            $this->view->loginForm = $form;
        }
       
    }

    /**
     * User Logout action
     */
    public function logoutAction()
    {
        $this->_auth->clearIdentity();
        $this->_redirect('/');
    }

    /**
     * Return doctrine based auth adapter
     * 
     * @param string $username
     * @param string $password
     * @return ZendX_Doctrine_Auth_Adapter 
     */
    protected function _getAuthAdapter($username, $password)
    {
        $userModel = new Model\Users();
        $authAdapter = new \Slys\Authentication\Adapter\Doctrine($userModel->getEntityManager());
        $authAdapter->setEntityName('\User\Model\Mapper\User')
                ->setIdentityField('login')
                ->setCredentialField('password')
                ->setIdentity($username)
                ->setCredential($password);

        return $authAdapter;
    }
}