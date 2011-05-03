<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: User.php 1232 2011-04-17 21:00:36Z deeper $
 * @license New BSD
 */
use Doctrine\ORM\EntityRepository;

class User_Model_DbTable_User extends EntityRepository 
{

    /**
     * Return user entity with role
     * @param int $id
     * @return  User_Model_Mapper_User
     */
    public function getUser($id) {
        return Doctrine_Query::create()
                        ->select()
                        ->from('User_Model_Mapper_User user')
                        ->leftJoin('user.Role role')
                        ->addWhere('user.id = ?', array($id))
                        ->fetchOne();
    }

    public function getUsers() {
        $query = Doctrine_Query::create()
                ->select('user.*, role.*')
                ->from('User_Model_Mapper_User user')
                ->leftJoin('user.Role role');

        return $query->execute();
    }

    public function getPager($page = 1, $maxPerPage = 20) {
        $query = Doctrine_Query::create()
                ->select('user.*, role.*')
                ->from('User_Model_Mapper_User user')
                ->leftJoin('user.Role role');

        return new Doctrine_Pager($query, $page, $maxPerPage);
    }

}

