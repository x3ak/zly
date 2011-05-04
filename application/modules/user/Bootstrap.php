<?php

/**
 * Slys
 *
 * Users authentification and ACL support for the Slys applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */
class User_Bootstrap extends Zend\Application\Module\Bootstrap 
    implements Slys_Api_Request_Requestable, 
               Slys_Application_Module_Installable,  
               Slys_Application_Module_Updateable
{
    
    protected function _initAcl()
    {
        $this->bootstrap('Frontcontroller');
        $this->getResource('Frontcontroller')->registerPlugin(
                new User_Plugin_Acl(new Zend_Acl())
        );
    }

    public function onRequest(Slys_Api_Request $request)
    {
        switch ($request->getName()) {
            case 'navigation.get-module-navigation':
                $types = $this->getResourceLoader()->getResourceTypes();
                $navigationPath = $types['config']['path'].DIRECTORY_SEPARATOR.'navigation.yml';
                if(is_file($navigationPath)) {
                    $navigation = new Zend_Navigation_Page_Mvc(new Zend_Config_Yaml($navigationPath));
                    $navName = $navigation->getLabel();
                    if(!empty($navName))
                        $request->getResponse()->setData($navigation);
                }
            break;
        }
    }
    
    public function install()
    {
        return true;
    }
    
    public function update()
    {
        return 'User updated';
    }
    
    public function uninstall()
    {
        return 'User uninstalled';
    }

}