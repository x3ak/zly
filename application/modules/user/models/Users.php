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
    
    public function dropSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);
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

    /**
     * Return paginator for users list
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @return \Zend\Paginator\Paginator 
     */
    public function getUsersPaginator($pageNumber = 1, $itemCountPerPage = 20)
    {
        $repo = $this->getEntityManager()->getRepository('\User\Model\Mapper\User');
        $paginator = new \Zend\Paginator\Paginator($repo->getPaginatorAdapter());
        $paginator->setCurrentPageNumber($pageNumber)->setItemCountPerPage($itemCountPerPage);
        return $paginator;
    }

    /**
     * Save profile fields
     * @param User_Model_Mapper_User $user
     * @param array $data
     * @return boolean
     */
    public function saveProfile(Mapper\User $user, $data)
    {
        $ignoredFields = array('role_id', 'login', 'active','password');

        foreach ($data as $key=>$value) {
            if(in_array($key, $ignoredFields))
                unset($data[$key]);
        }
        return $this->saveUser($user, $data);
    }
    
    /**
     * Save user data
     * @param User_Model_Mapper_User $user
     * @param array $data
     * @return boolean
     */
    public function saveUser(Mapper\User $user, $data)
    {
        $user->fromArray($data);
        
        if(!empty($data['role_id'])) {
            $role = $this->getEntityManager()->find('\User\Model\Mapper\Role', $data['role_id']);
            if(!empty($role))
                $user->setRole($role);
        }
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->persist($user);
        return $this->getEntityManager()->flush();
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
    
    /**
     * Delete user row from DB
     * @param Mapper\User $user
     * @return boolean 
     */
    public function deleteUser(Mapper\User $user)
    {
        $this->getEntityManager()->remove($user);
        return $this->getEntityManager()->flush();
    }
    
    public function createDefaultUser($userName, $userPassword, $userRoleName, $guestRoleName) 
    {

            $apiRequest = new \Slys\Api\Request($this,  'sysmap.get-root-identifier');
            $rootNode = $apiRequest->proceed()->getResponse()->getFirst();
              
            $guestRole = new Mapper\Role();
            $guestRole->setName($guestRoleName);
            $guestRole->setIsDefault(true);
            $this->getEntityManager()->persist($guestRole);
            $this->getEntityManager()->flush();
            
            $userRole = new Mapper\Role();
            $userRole->setName($userRoleName);
            $userRole->setParentId($guestRole->getId());
            $this->getEntityManager()->persist($userRole);
            $this->getEntityManager()->flush();
            
            $rule = new Mapper\Rule();
            $rule->setResourceId($rootNode);
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