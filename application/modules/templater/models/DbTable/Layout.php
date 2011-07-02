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
        $query = $qb->leftJoin('lay.theme', 'tpl')
                    ->leftJoin('lay.points','lp')
                    ->andWhere('lay.published = :published')
                    ->andWhere('tpl.current = :current')
                    ->setParameters(array('published'=>true, 'current'=>true))
                    ->orderBy('lp.map_id','DESC');

        $layoutParts = array();
        foreach($identifiers as $identifier) {
            if($identifier instanceof \Zend\Acl\Resource\GenericResource)
            $layoutParts[] = $identifier->getResourceId();
        }

        $layoutParts = $qb->expr()->in('lp.map_id', $layoutParts);
        $query->andWhere($layoutParts);

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

        $params = array(
            'published' => true,
            'current' => true,
            'name' => $defaultLayoutName
        );
        $query = $this->createQueryBuilder('lay')
            ->leftJoin('lay.Theme', 'tpl')
            ->andWhere('lay.published = :published')
            ->andWhere('tpl.current = current')
            ->andWhere('lay.name = :name')
            ->setParameters(array('published'=>true))
            ->getQuery();
        return $query->getSingleResult();
    }

    /**
     * Return paginator for Layout mapper
     * @return \Slys\Paginator\Adapter\Doctrine2 
     */
    public function getPaginatorAdapter()
    {
        $query = $this->createQueryBuilder('layout')->select('layout', 'theme')
                      ->leftJoin('layout.theme', 'theme')->getQuery();
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
                    ->select('lay','lp')
                    ->leftJoin('lay.points', 'lp')
                    ->where('lay.id = :layId')
                    ->setParameter('layId', $layId)
                    ->getQuery()
                    ->getSingleResult();
    }
    
    public function getLayoutWithWidgetsbyNameAndRequest($layoutName, $mapIds = array())
    {
        $ids = array();

        foreach((array)$mapIds as $mapId) {
            if($mapId instanceof \Zend\Acl\Resource\GenericResource)
                $ids[] = $mapId->getResourceId();
        }
        $qb = $this->createQueryBuilder('lay');
        $idsParts = $qb->expr()->in('lp.map_id', ':ids');
        $query = $qb->select('lay', 'w', 'wp', 'wt')
                    ->innerJoin('lay.widgets w')
                    ->innerJoin('w.points wp')
                    ->andWhere($idsParts)
                    ->addOrderBy('wp.map_id','DESC')
                    ->setParameter('ids');

        return $query->fetchOne();
    }

}

