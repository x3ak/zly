<?php

/**
 * Zly
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 985 2011-01-06 08:23:52Z deeper $
 * @license New BSD
 */
namespace Templater\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class LayoutPoint extends EntityRepository
{

    /**
     * Remove from DB layout points which not in new points set
     * @param int $layId
     * @param array $newPoints
     * @return boolean
     */
    public function deleteUnusedPoints($layId, array $newPoints)
    {
        $qb = $this->createQueryBuilder('lp');
        $pointsPart = $qb->expr()->in('lp.map_id', ':points');

        $query = $qb ->andWhere('lp.layout_id = :layoutId')
                     ->andWhere($pointsPart)
                     ->setParameter('layoutId', $layId)
                     ->setParameter('points', $newPoints)
                     ->getQuery();

        $points = $query->execute();
        foreach($points as $point)
            $this->getEntityManager()->remove($point);
        return $this->getEntityManager()->flush();
    }
}

