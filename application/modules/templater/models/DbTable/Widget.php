<?php

/**
 * SlyS
 * 
 * @version $Id: Widget.php 1231 2011-04-17 17:49:48Z deeper $
 * @license New BSD
 */
namespace Templater\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class Widget extends EntityRepository
{
    public function getWidgets()
    {
        $this->createQueryBuilder('wd')->select('wd','lay')->leftJoin('wd.layout lay');
        return $query->execute();
    }

    public function getLayoutWithWidgetsbyNameAndRequest($layoutName, $mapIds = array())
    {
        $ids = array();

        foreach((array)$mapIds as $mapId) {
            if($mapId instanceof Sysmap_Model_Mapper_Sysmap)
                $ids[] = $mapId->hash;
        }

        $query = Doctrine_Query::create()
                        ->select('lay.*, w.*, wp.*, wt.* ')
                        ->from('Templater_Model_Mapper_Layout lay')
                        ->innerJoin('lay.Widgets w')
                        ->innerJoin('w.WidgetPoints wp')
                        ->whereIn('wp.map_id', $ids)
                        ->addOrderBy('wp.map_id DESC');

        return $query->fetchOne();
    }

    /**
     * Return widget by id with all widget points
     * @param int $wdId
     * @return Templater_Model_Mapper_Widget
     */
    public function getWidgetWithWidgetPoints($wdId)
    {
        return $this->createQuery('wd')
                    ->leftJoin('wd.WidgetPoints wp')
                    ->addWhere('wd.id = ?', array($wdId))
                    ->fetchOne();
    }
    
    /**
     * Return paginator for widget mapper
     * @return \Slys\Paginator\Adapter\Doctrine2 
     */
    public function getPaginatorAdapter()
    {
        $query = $this->createQueryBuilder('wd')
                      ->select('wd','lay','theme')
                      ->leftJoin('wd.layout', 'lay')
                      ->leftJoin('lay.theme', 'theme')
                      ->getQuery();
        return new \Slys\Paginator\Adapter\Doctrine2($query);
    }
}

