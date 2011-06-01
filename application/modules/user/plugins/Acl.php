<?php

/**
 * Slys
 *
 * Acl plugin for restrict access to resource
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */
namespace User\Plugin;

class Acl extends \Zend\Controller\Plugin\AbstractPlugin
{

    /**
     * Copy of ACL object
     * @var User_Library_Acl
     */
    protected $_acl;
    /**
     * Copy of identity object
     * @var Doctine_Record|array|null
     */
    protected $_identity;

    /**
     * Current role instance
     * @var Zend_Acl_Role
     */
    protected $_currentRole;
    
    /**
     * Authentification service
     * @var \Zend\Authentication\AuthenticationService 
     */
    protected $_auth;

    /**
     * Contructor waiting Zend_Acl instance
     * @param Zend_Acl $acl
     */
    public function __construct(\Zend\Acl\Acl $acl, \Zend\Authentication\AuthenticationService $authenticationService = null)
    {
        $this->_acl = $acl;
        if(empty($authenticationService))
        $this->_auth = new \Zend\Authentication\AuthenticationService();
    }

    /**
     * Rerturn current ACL object
     * @return User_Library_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }
    
    /**
     * Return application authentification service
     * @return \Zend\Authentication\AuthenticationService 
     */    
    public function getAuthentificationService()
    {
        return $this->_auth;
    }

    /**
     * Check if allowed current request
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param string $params
     * @return boolean
     */
    public function isAllowed($resource)
    {
        if(!$this->_acl->has($resource)){

            $apiRequest = new Slys_Api_Request($this, 
                                                'sysmap.get-item-parents-by-identifier',
                                                array('identifier'=>$resource));
            $sysmap = $apiRequest->proceed()->getResponse()->getFirst();
            
            if(empty($sysmap))
                return false;

            foreach($sysmap as $parentResource) {

                $parentItem = $parentResource->getMapIdentifier();

                if($parentResource == $parentItem)
                    return false;

                if(!$this->_acl->has($parentItem)) {
                    $this->_acl->addResource($parentItem);
                    $this->setRules($this->_currentRole, array($parentItem));
                }

                $allow = $this->_acl->isAllowed($this->_currentRole, $parentItem);
                
                if($allow)
                    return true;
            }

           return false;
        }
        return $this->_acl->isAllowed($this->_currentRole, $resource);
    }

    /**
     * Prepare ACL before route startup and check if current request is allowed
     * 
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(\Zend\Controller\Request\AbstractRequest $request)
    {
        /**
         * Init ACL it now not in contructor because possible some resources not initilized
         */
        $this->_initAcl();
        
        \Zend\View\Helper\Navigation\AbstractHelper::setDefaultAcl($this->_acl);

        if($this->_auth->hasIdentity()) {
            if (!empty($this->_auth->getIdentity()->Role->name))
                $this->_currentRole = new \Zend\Acl\GenericRole(Zend_Auth::getInstance()->getIdentity()->Role->name);
        }

        if (empty($this->_currentRole)) {
                trigger_error ( 'Please provide default user role' );
         }
        \Zend\View\Helper\Navigation\AbstractHelper::setDefaultRole($this->_currentRole);

        $allow = false;
        foreach($this->_acl->getResources() as $resource) {
            if($this->_acl->isAllowed($this->_currentRole, $resource))
                $allow = true;
        }

        if(!$allow) {
            $routeName = \Zend\Controller\Front::getInstance()->getRouter()->getCurrentRouteName();
            $front = \Zend\Controller\Front::getInstance();
            if ($routeName == 'admin')
                $controller = 'admin';
            else
                $controller = 'index';

            $request->setActionName('login')
                    ->setControllerName($controller)
                    ->setModuleName('user');
        }
    }


    /**
     * Get ACL information from DB
     * @return User_Plugin_Acl
     */
    protected function _initAcl()
    {
        $userModel = new \User\Model\Roles();
        
        $rolesRows = $userModel->getRoles();

        $roles = array();
        foreach($rolesRows as $roleRow) {
            $roles[$roleRow->getId()] = $roleRow;
            if($roleRow->getIs_default())
                $this->_currentRole = new \Zend\Acl\Role\GenericRole($roleRow->getName());
        }

        foreach($roles as $role) {
            $parent = null;
            if(!empty($role->parent_id)) {
                $parent = $roles[$role->parent_id]->name;
            }

            $this->_acl->addRole(new \Zend\Acl\Role\GenericRole($role->getName()), $parent);
        }

        $apiRequest = new \Slys\Api\Request($this, 'sysmap.currently-active-items');

        foreach($apiRequest->proceed()->getResponse()->getFirst() as $resource) {
            if($resource instanceof \Zend\Acl\Resource\GenericResource) {
                $this->_acl->addResource($resource->getMapIdentifier());
            }
        }

        foreach($this->_acl->getRoles() as $role) {
            $this->setRules($role, $this->_acl->getResources());
        }

        return $this;
    }

    /**
     * Set ACL rule from DB
     * @param string $role
     * @param string $resourceId
     * @return User_Plugin_Acl
     */
    protected function setRules($role, $resources)
    {
        $userModel = new \User\Model\Roles();
        if(empty($resources))
            return $this;
        
        $rules = $userModel->getRulesByRoleAndResources($role, $resources);
        if($rules->count() > 0) {
            foreach($rules as $rule) {
                $this->_acl->{$rule->rule}($role, $rule->resource_id);
            }

        }
        return $this;
    }

}