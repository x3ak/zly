<?php

/**
 * SlyS
 * 
 * @version $Id: Generator.php 985 2011-01-06 08:23:52Z deeper $
 * @license New BSD
 */
namespace Templater\Model\Mapper;

/**
 * @Entity(repositoryClass="Templater\Model\DbTable\WidgetPoint")
 * @Table(name="templater_widget_points")
 */
class WidgetPoint
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /** @Column(length=35) */
    protected $map_id;
    /** @Column(type="integer", nullable=true) */
    protected $widget_id;
    /**
     * @ManyToOne(targetEntity="Templater\Model\Mapper\Widget")
     * @JoinColumn(name="widget_id", referencedColumnName="id", unique=false)
     */
    protected $widget;
    
    public function getId()     {
        return $this->id;
    }

    public function getMapId()
    {
        return $this->map_id;
    }

    public function setMapId($map_id)
    {
        $this->map_id = $map_id;
    }

    public function getWidgetId()
    {
        return $this->widget_id;
    }

    public function setWidgetId($widget_id)
    {
        $this->widget_id = $widget_id;
    }

    public function getWidget()
    {
        return $this->widget;
    }

    public function setWidget($widget)
    {
        $this->widget = $widget;
    }


}

