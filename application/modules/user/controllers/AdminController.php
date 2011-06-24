<?php

/**
 * SlyS
 *
 * @abstract   contains User_AdminController class, extending Zend
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: AdminController.php 1189 2011-02-10 16:26:25Z deeper $
 */

namespace User;

/**
 * User administrator panel
 */

class AdminController extends \Zend\Controller\Action
{

    /**
     * User module admin dashboard
     */
    public function indexAction()
    {

    }

    /**
     * Administrator login action
     */
    public function loginAction()
    {
        $this->_forward('login', 'index');
    }

    /**
     * Users list
     */
    public function usersAction()
    {
        $usersModel = new Model\Users();
        $this->view->pager = $usersModel->getUsersPaginator(
            $this->getRequest()->getParam('page', 1),
            $this->getRequest()->getParam('perPage', 20)
        );
    }

    /**
     * Edit user action
     * @return null
     */
    public function editUserAction()
    {
        $form = new Form\User();
        $usersModel = new Model\Users();
        $rolesModel = new Model\Roles();

        $id = $this->getRequest()->getParam('id');

        if (!empty($id)) {
            $user = $usersModel->getUser($id);
        } else {
            $user = new Model\Mapper\User();
        }

        foreach ($rolesModel->getRoles() as $role)
            $form->getElement('role_id')->addMultiOption($role->getId(), $role->getName());

        $form->populate($user->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            if ($this->getRequest()->getParam('password'))
                $this->getRequest()->setParam('password', md5($this->getRequest()->getParam('password')));
            $usersModel->saveUser($user, $this->getRequest()->getParams());
            $this->broker('FlashMessenger')->addMessage('User successful saved.');
            $this->broker('redirector')->goToRoute(array('module' => 'user', 'action' => 'users'), 'admin', true);
            return;
        }

        $this->view->editUserForm = $form;
    }

    /**
     * Delete user action
     */
    public function deleteUserAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $usersModel = new Model\Users();
            $user = $usersModel->getUser($id);
            $result = $usersModel->deleteUser($user);
        }
        $this->broker('FlashMessenger')->addMessage('User successful deleted.');
        $this->broker('redirector')->goToRoute(array('module' => 'user', 'action' => 'users'), 'admin', true);
    }

    public function rolesAction()
    {
        $rolesModel = new Model\Roles();
        $this->view->pager = $rolesModel->getRolesPaginator(
            $this->getRequest()->getParam('page', 1),
            $this->getRequest()->getParam('perPage', 20)
        );
    }

    /**
     * Edit user action
     * @return null
     */
    public function editRoleAction()
    {
        $form = new Form\Role();
        $rolesModel = new Model\Roles();

        $role = $rolesModel->getRole($this->getRequest()->getParam('id'), true);

        $form->populate($role->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $result = $rolesModel->saveRole($role, $form->getValues());
            if($result)
                $this->broker('FlashMessenger')->addMessage('Role successful saved.');
            $this->broker('redirector')->goToRoute(array('module' => 'user', 'action' => 'roles'), 'admin', true);
            return;
        }

        $this->view->editRoleForm = $form;
    }

    /**
     * Delete role action
     */
    public function deleteRoleAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!empty($id)) {
            $rolesModel = new User_Model_Roles();
            $role = $rolesModel->getRole($id);
            $rolesModel->deleteRole($role);
        }
        $this->broker('FlashMessenger')->addMessage('Role successful deleted.');
        $this->broker('redirector')->goToRoute(array('module' => 'user', 'action' => 'roles'), 'admin', true);
    }

    /**
     * Setting display action
     * @Qualifier \User\Form\Widget\UserBox
     */
    public function settingsAction()
    {
        
    }
}