<?php

namespace Navigation\Model\Mapper;

use Zly\Doctrine\NestedSet\Node;

/**
 * @Zly:Tree(type="nested")
 * @Entity(repositoryClass="Navigation\Model\DbTable\Item")
 * @Table(name="navigation_items")
 */
class Item
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
    /** @Column(length=255, nullable=true) */
    protected $external_link;
    /** @Column(length=50, nullable=true) */
    protected $sysmap_identifier;
    /** @Column(length=30, nullable=true) */
    protected $route;
    /** @Column(type="boolean", nullable=true) */
    protected $read_only;
    /** @Column(type="integer", nullable=true) */
    protected $lft;
    /** @Column(type="integer", nullable=true) */
    protected $rgt;
    /**
     * @Column(type="integer", nullable=true)
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
    
    public function toArray()
    {
        $array = array();
        $filter = new \Zend\Filter\Word\SeparatorToCamelCase('_');

        $vars = get_class_vars(get_class($this));
        foreach (array_keys($vars) as $var) {
            $array[$var] = $this->{'get' . $filter->filter($var)}();
        }
        return $array;
    }

    public function fromArray($data)
    {
        $filter = new \Zend\Filter\Word\SeparatorToCamelCase('_');

        $vars = get_class_vars(get_class($this));
        foreach (array_keys($vars) as $var) {
            if (isset($data[$var]))
                $this->{'set' . $filter->filter($var)}($data[$var]);
        }

        return $this;
    }
}

