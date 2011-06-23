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
        $form = new User_Form_User();
        $usersModel = new User_Model_Users();
        $rolesModel = new User_Model_Roles();

        $id = $this->getRequest()->getParam('id');

        if (!empty($id)) {
            $user = $usersModel->getUser($id);
        } else {
            $user = new User_Model_Mapper_User();
        }

        foreach ($rolesModel->getList() as $role)
            $form->getElement('role_id')->addMultiOption($role->id, $role->name);

        $form->populate($user->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            if ($this->getRequest()->getParam('password'))
                $this->getRequest()->setParam('password', md5($this->getRequest()->getParam('password')));
            $user->fromArray($this->getRequest()->getParams());
            $user->save();
            $this->_helper->getHelper('FlashMessenger')->addMessage('User successful saved.');
            $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'users'), 'admin', true);

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
            $usersModel = new User_Model_Users();
            $user = $usersModel->getUser($id);
            $user->delete();
        }
        $this->_helper->getHelper('FlashMessenger')->addMessage('User successful deleted.');
        $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'users'), 'admin', true);
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
        $form = new User_Form_Role();
        $rolesModel = new User_Model_Roles();

        $role = $rolesModel->getRole($this->getRequest()->getParam('id'), true);

        $form->populate($role->toArray());

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $result = $rolesModel->saveRole($role, $form->getValues());
            if($result)
                $this->_helper->getHelper('FlashMessenger')->addMessage('Role successful saved.');
            $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'roles'), 'admin', true);
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
            $role->delete();
        }
        $this->_helper->getHelper('FlashMessenger')->addMessage('Role successful deleted.');
        $this->_helper->getHelper('redirector')->goToRoute(array('module' => 'user', 'action' => 'roles'), 'admin', true);
    }

    /**
     * Setting display action
     * @Qualifier \User\Form\Widget\UserBox
     */
    public function settingsAction()
    {
        
    }
}