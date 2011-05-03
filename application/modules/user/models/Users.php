<?php

/**
 * Slys
 *
 * @version    $Id: Users.php 1212 2011-03-03 13:53:33Z deeper $
 */
class User_Model_Users extends Slys_Doctrine_Model
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
    
}