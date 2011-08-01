<?php

/**
 * Zly
 *  
 * @version $Id: Item.php 269 2010-10-05 13:38:46Z deeper $
 * @license New BSD
 */
namespace Navigation\Model\DbTable;

use Zly\Doctrine\NestedSet as NestedSet;

class Item extends \Doctrine\ORM\EntityRepository
{
    
    public function getTree($id = 1)
    {
        return $this->nsm->fetchTree($id);
    }
    
    public function getTreeAsArray()
    {
        return array();
    }
    
    public function wrapNode($node)
    {
        return $this->nsm->wrapNode($node);
    }
    
    public function createRoot(\Navigation\Model\Mapper\Item $node)
    {
        $rootNode = $this->nsm->createRoot($node);
        $this->getEntityManager()->persist($node);
        return $this->getEntityManager()->flush();
    }
}

