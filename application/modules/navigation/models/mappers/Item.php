<?php

namespace Navigation\Model\Mapper;

use Zly\Doctrine\NestedSet\Node;

/**
 * @Entity(repositoryClass="Navigation\Model\DbTable\Item")
 * @Table(name="navigation_items")
 */
class Item implements Node
{

    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /** @Column(length=50) */
    protected $type;
    /** @Column(length=255) */
    protected $title;
    /** @Column(length=255) */
    protected $external_link;
    /** @Column(length=50) */
    protected $sysmap_identifier;
    /** @Column(length=30) */
    protected $route;
    /** @Column(type="boolean") */
    protected $read_only;
    /** @Column(type="integer") */
    protected $lft;
    /** @Column(type="integer") */
    protected $rgt;
    /**
     * @Column(type="integer")
     */
    private $root;
    
    public function getId()     
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getExternalLink()
    {
        return $this->external_link;
    }

    public function setExternalLink($external_link)
    {
        $this->external_link = $external_link;
    }

    public function getSysmapIdentifier()
    {
        return $this->sysmap_identifier;
    }

    public function setSysmapIdentifier($sysmap_identifier)
    {
        $this->sysmap_identifier = $sysmap_identifier;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getReadOnly()
    {
        return $this->read_only;
    }

    public function setReadOnly($read_only)
    {
        $this->read_only = $read_only;
    }

    public function getLeftValue()
    {
        return $this->lft;
    }

    public function setLeftValue($lft)
    {
        $this->lft = $lft;
    }

    public function getRightValue()
    {
        return $this->rgt;
    }

    public function setRightValue($rgt)
    {
        $this->rgt = $rgt;
    }

    public function __toString() 
    {
        return $this->title;
    }
    
    public function getRootValue() 
    { 
        return $this->root; 
    }
    public function setRootValue($root) 
    { 
        $this->root = $root;
    }
}

