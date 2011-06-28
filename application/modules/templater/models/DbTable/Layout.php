<?php

/**
 * SlyS
 *
 * @version $Id: Layout.php 1097 2011-01-24 08:38:38Z criolit $
 * @license New BSD
 */

namespace Templater\Model\DbTable;

use Doctrine\ORM\EntityRepository;

class Layout extends EntityRepository
{
    public function getLayoutWithWidgetsByName($name)
    {
        return $this->createQueryBuilder('lay')
                ->leftJoin('lay.widgets wd')
                ->where('lay.name = ?', array($name))
                ->orderBy('wd.ordering')
                ->getQuery()
                ->getSingleResult();
    }

    /**
     * Return layout attached to current map indentifiers
     * @param array $identifiers
     * @return \Templater\Model\Mapper\Layout
     */
    public function getCurrentLayout($identifiers)
    {
        $qb = $this->createQueryBuilder('lay');
        $query = $qb->leftJoin('lay.Theme tpl')
                    ->leftJoin('lay.Points lp')
                    ->where('lay.published = ?', array(true))
                    ->where('tpl.current = ?', array(true))
                    ->orderBy('lp.map_id','DESC');

        $layoutParts = array();
        foreach($identifiers as $identifier) {
            $layoutParts[] = $identifier->getMapIdentifier();
        }

        $layoutParts = $qb->expr()->in('lp.map_id', $layoutParts);
        $query->where($layoutParts);

        $layout = $query->getQuery()->getSingleResult();

        if(empty($layout))
            return $this->getDefaultLayout();
        return $layout;
    }

    /**
     * Return default layout for current theme by default options
     * @return \Templater\Model\Mapper\Layout
     */
    public function getDefaultLayout()
    {
        $defaults = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getOption('templater');
        $routeName = \Zend\Controller\Front::getInstance()->getRouter()->getCurrentRouteName();
        $defLayName = 'default';
        if($routeName == 'admin')
            $defLayName = $routeName;

        $defaultLayoutName = $defaults['layout'][$defLayName];

        $query = $this->createQueryBuilder('lay')
            ->leftJoin('lay.Theme tpl')
            ->where('lay.published = ?', array(true))
            ->where('tpl.current = ?', array(true))
            ->where('lay.name = ?', array($defaultLayoutName))
             ->getQuery();
        return $query->getSingleResult();
    }

    /**
     * Return paginator for Layout mapper
     * @return \Slys\Paginator\Adapter\Doctrine2 
     */
    public function getPaginatorAdapter()
    {
        $query = $this->createQueryBuilder('layout')->getQuery();
        return new \Slys\Paginator\Adapter\Doctrine2($query);
    }

    /**
     * Return layout by id with all layout points
     * @param int $layId
     * @return \Templater\Model\Mapper\Layout
     */
    public function getLayoutWithLayoutPoints($layId)
    {
        return $this->createQueryBuilder('lay')
                    ->leftJoin('lay.points lp')
                    ->where('lay.id = ?', array($layId))
                    ->getQuery()
                    ->getSingleResult();
    }

}

