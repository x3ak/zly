<?php

/**
 * Zly
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 761 2010-12-14 11:49:54Z deeper $
 * @license New BSD
 */
namespace User\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class Rule extends EntityRepository 
{

    /**
     * Get rules by role and resources
     * @param string $role
     * @param array $resources
     * @return Doctrine_Collection
     */
    public function getRoleRules($role, array $resources) 
    {

        $qb = $this->createQueryBuilder('rule');
        $resourcesPart = $qb->expr()->in('rule.resource_id', $resources);
        return $qb->innerJoin('rule.role', 'role')
                  ->andWhere('role.name = :role')
                  ->andWhere($resourcesPart)
                  ->setParameter('role', $role)
                  ->getQuery()
                  ->execute();
    }

    /**
     * Remove from DB rules which resources not in new resources set
     * @param int $itemId
     * @param array $newItems
     * @return boolean
     */
    public function deleteUnusedRules($itemId, array $newItems) {
        $newItems[] = 'dummy';
        $items = $this->createQuery('items')
                ->whereNotIn('items.resource_id', $newItems)
                ->addWhere('items.role_id = ?', array($itemId))
                ->execute();
        return $items->delete();
    }

}

