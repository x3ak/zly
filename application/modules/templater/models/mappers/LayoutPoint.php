<?php

/**
 * Zly
 * 
 * @version $Id: Generator.php 985 2011-01-06 08:23:52Z deeper $
 * @license New BSD
 */
namespace Templater\Model\Mapper;

/**
 * @Entity(repositoryClass="Templater\Model\DbTable\LayoutPoint")
 * @Table(name="templater_layout_points")
 */
class LayoutPoint
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
    protected $layout_id;
    /**
     * @ManyToOne(targetEntity="Templater\Model\Mapper\Layout")
     * @JoinColumn(name="layout_id", referencedColumnName="id", unique=false)
     */
    protected $layout;
    
    public function getId()     
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getMapId()
    {
        return $this->map_id;
    }

    public function setMapId($map_id)
    {
        $this->map_id = $map_id;
    }

    public function getLayoutId()
    {
        return $this->layout_id;
    }

    public function setLayoutId($layout_id)
    {
        $this->layout_id = $layout_id;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
}

