<?php

/**
 * Slys
 *
 * @version    $Id: Users.php 1212 2011-03-03 13:53:33Z deeper $
 */
namespace User\Model;

class Users extends \Slys\Doctrine\Model
{

    public function getlist()
    {
        return User_Model_DbTable_User::getInstance()->getUsers();
    }

    public function getUser($id)
    {
        return User_Model_DbTable_User::getInstance()->findOneById($id);
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
            //TODO: receive next node id from sysmap module 
            $rootNode = '0-816563134a61e1b2c7cd7899b126bde4';
              
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
            $this->getEntityManager()->persist($rule);            
            $this->getEntityManager()->flush();
            
            $user = new Mapper\User();
            $user->setActive(true);
            $user->setPassword(md5($userPassword));
            $user->setLogin($userName);
            $user->setRole_id($userRole->getId());
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            
            return $this;
    }
    
}