<?php

/**
 * Slys
 *
 * @version    $Id: Widgets.php 1056 2011-01-19 14:38:17Z deeper $
 */
namespace Templater\Model;

class Widgets extends \Slys\Doctrine\Model
{

    /**
     * Widgets repository
     * @var \Templater\Model\DbTable\Widget
     */
    protected $_repository;
    
    public function __construct()
    {
        $this->_repository = $this->getEntityManager()->getRepository('\Templater\Model\Mapper\Widget');
    }
    
    /**
     * Return list of all widgets
     *
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return $this->_repository->findAll();
    }

    /**
     * Return widget entity
     *
     * @param int $id
     * @param boolean $forEdit
     * @return Templater_Model_Mapper_Widget
     */
    public function getWidget($id, $forEdit = false)
    {
        if (empty($id) && $forEdit)
            $widget = new Mapper\Widget();
        else
            $widget = $this->_repository->getWidgetWithWidgetPoints($id);

        if (empty($widget) && $forEdit)
            $widget = new Mapper\Widget();

        return $widget;
    }

    /**
     * Save widget type
     * @param array $values
     * @return boolean
     */
    public function saveWidget(Mapper\Widget $widget, $values)
    {
        $this->getEntityManager()->getRepository('\Templater\Model\Mapper\WidgetPoint')
             ->deleteUnusedPoints($widget->getId(), $values['widget_points']);

        $widget->fromArray($values);

        if(!empty($values['widget_points'])) {
            foreach($values['widget_points'] as $key=>$mapId) {
                $point = $this->_repository->findOneBy(array('map_id' => $mapId, 'widget_id'=>$widget->getId()));
                
                if(empty($point)) {
                    $point = new Mapper\WidgetPoint();
                    $point->setMapId($mapId);
                    $this->getEntityManager()->persist($point);
                    $widget->getWidgetPoints()->add($point);
                }
            }
        }
        
        return $this->getEntityManager()->flush();
    }

    /**
     * Return paginator for widgets list
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @return \Zend\Paginator\Paginator 
     */
    public function getWidgetsPaginator($pageNumber = 1, $itemCountPerPage = 20)
    {
        $repo = $this->getEntityManager()->getRepository('Templater\Model\Mapper\Widget');
        $paginator = new \Zend\Paginator\Paginator($repo->getPaginatorAdapter());
        $paginator->setCurrentPageNumber($pageNumber)->setItemCountPerPage($itemCountPerPage);
        return $paginator;
    }
    
     /**
     * Delete Widget
     * @param int $id
     * @return boolean
     */
    public function deleteWidget($id)
    {
        $widget = new Templater_Model_Mapper_Widget();
        $widget->assignIdentifier($id);
        return $widget->delete();
    }

}