<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Role.php 1232 2011-04-17 21:00:36Z deeper $
 * @license New BSD
 */
namespace User\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class Role extends EntityRepository 
{

    /**
     * Return role marked as default
     * @return User_Model_Mapper_Role
     */
    public function getDefaultRole() {
        return $this->findOneByDefaul(1);
    }

    /**
     * Return one user role by ID with rules
     * @param int $id
     * @return User_Model_Mapper_Role
     */
    public function getRole($id) {
        return $this->createQuery('role')
                        ->select('role.*, rule.*')
                        ->leftJoin('role.Rules rule')
                        ->addWhere('role.id = ?', $id)
                        ->fetchOne();
    }

    /**
     * Return all roles list
     * @return Doctrine_Collection
     */
    public function getRoles() {
        
        return $this->createQueryBuilder('role')
                    ->addOrderBy('role.parent_id','ASC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * Return paginator for User mapper
     * @return \Slys\Paginator\Adapter\Doctrine2 
     */
    public function getPaginatorAdapter()
    {
        $query = $this->createQueryBuilder('role')->getQuery();
        return new \Slys\Paginator\Adapter\Doctrine2($query);
    }

}

