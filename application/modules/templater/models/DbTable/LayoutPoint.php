<?php

/**
 * SlyS
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
        $newPoints = $qb->expr()->in('lp.map_id', $newPoints);

        $points = $qb
                     ->where('lp.layout_id = ?', $layId)
                     ->where($newPoints)
                     ->getQuery()
                     ->execute();
        foreach($points as $point)
            $this->getEntityManager()->remove($point);
        return $this->getEntityManager()->flush();
    }
}

