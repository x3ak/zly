<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Layout.php 269 2010-10-05 13:38:46Z deeper $
 * @license New BSD
 */
namespace Templater\Model\Mapper;

/**
 * @Entity(repositoryClass="Templater\Model\DbTable\Layout")
 * @Table(name="templater_layouts")
 */
class Layout
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /** @Column(length=255) */
    protected $title;
    /** @Column(length=255) */
    protected $name;
    /** @Column(type="integer", nullable=true) */
    protected $theme_id;
    /** @Column(length=1000) */
    protected $params;
    /** @Column(type="boolean") */
    protected $published;
    /**
     * @ManyToOne(targetEntity="Templater\Model\Mapper\Theme")
     * @JoinColumn(name="theme_id", referencedColumnName="id", unique=false)
     */
    protected $theme;
    /**
     * @OneToMany(targetEntity="Templater\Model\Mapper\Widget", mappedBy="layout")
     */
    protected $widgets;    
    /**
     * @OneToMany(targetEntity="Templater\Model\Mapper\LayoutPoint", mappedBy="layout")
     */
    protected $points;

    public function getId()     {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getThemeId()
    {
        return $this->theme_id;
    }

    public function setThemeId($theme_id)
    {
        $this->theme_id = $theme_id;
    }

    public function getParams()
    {
        return unserialize($this->params);
    }

    public function setParams($params)
    {
        $this->params = serialize($params);
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        $this->published = $published;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    public function getWidgets()
    {
        return $this->widgets;
    }

    public function setWidgets($widgets)
    {
        $this->widgets = $widgets;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;
    }


}

