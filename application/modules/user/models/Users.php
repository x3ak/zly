<?php

/**
 * Slys
 *
 * @version    $Id: Users.php 1212 2011-03-03 13:53:33Z deeper $
 */
namespace User\Model;

class Users extends \Slys\Doctrine\Model
{
    public function initSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);    
        $tool->createSchema($classes);
        return $this;
    }
    
    public function updateSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->updateSchema($classes);
        return $this;
    }
    
    protected function _getShemaClasses()
    {
        $em = $this->getEntityManager();
        $classes = array(
          $em->getClassMetadata('User\Model\Mapper\User'),
          $em->getClassMetadata('User\Model\Mapper\Role'),
          $em->getClassMetadata('User\Model\Mapper\Rule')
        );
        
        return $classes;
    }

    public function getlist()
    {
        return User_Model_DbTable_User::getInstance()->getUsers();
    }

    public function getUser($id)
    {
        return $this->getEntityManager()->find('\User\Model\Mapper\User', $id);
    }

    public function getUsersPager($page = 1, $maxPerPage = 20)
    {
        return User_Model_DbTable_User::getInstance()->getPager($page, $maxPerPage);
    }

    /**
     * Save profile fields
     * @param User_Model_Mapper_User $user
     * @param array $data
     * @return boolean
     */
    public function saveProfile(User_Model_Mapper_User $user, $data)
    {
        $ignoredFields = array('role_id', 'login', 'active','password');

        foreach ($data as $key=>$value) {
            if(in_array($key, $ignoredFields))
                unset($data[$key]);
        }
        $user->fromArray($data);
        return $user->save();
    }

    /**
     * Save user password with confirmation with old password
     * @param int $userId
     * @param string $newPassword
     * @param string $oldPassword
     * @return boolean
     */
    public function savePassword(User_Model_Mapper_User $user, $newPassword, $oldPassword = null)
    {
        if($user->password != md5($oldPassword)) {
            return false;
        }

        $user->password = md5($newPassword);
        $user->save();
        return true;
    }
    
    public function createDefaultUser($userName, $userPassword, $userRoleName, $guestRoleName) 
    {
            $apiRequest = new \Slys\Api\Request($this,  'sysmap.get-root-identifier');
            $rootNode = $apiRequest->proceed()->getResponse()->getFirst();
              
            $guestRole = new Mapper\Role();
            $guestRole->setName($guestRoleName);
            $guestRole->setIs_default(true);
            $this->getEntityManager()->persist($guestRole);
            $this->getEntityManager()->flush();
            
            $userRole = new Mapper\Role();
            $userRole->setName($userRoleName);
            $userRole->setParent_id($guestRole->getId());
            $this->getEntityManager()->persist($userRole);
            $this->getEntityManager()->flush();
            
            $rule = new Mapper\Rule();
            $rule->setResource_id($rootNode);
            $rule->setRule('allow');
            $rule->setRole($userRole);
            $this->getEntityManager()->persist($rule);            
            $this->getEntityManager()->flush();
            
            $user = new Mapper\User();
            $user->setActive(true);
            $user->setPassword(md5($userPassword));
            $user->setLogin($userName);
            $user->setRole($userRole);
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            
            return $this;
    }
    
}