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
    public function __construct(\Doctrine\ORM\EntityManager $em, $class)
    {
        $result = parent::__construct($em, $class);        
        $config = new NestedSet\Config($this->getEntityManager(), '\Navigation\Model\Mapper\Item');
        $this->nsm = new NestedSet\Manager($config);
        return $result;
    }
    
    public function getTree()
    {
        return $this->nsm->fetchTree(1);
    }
}

