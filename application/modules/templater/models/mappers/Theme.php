<?php

/**
 * SlyS
 * 
 * @version $Id: Theme.php 867 2010-12-22 12:44:26Z deeper $
 * @license New BSD
 */
namespace Templater\Model\Mapper;

/**
 * @Entity(repositoryClass="Templater\Model\DbTable\Theme")
 * @Table(name="templater_themes")
 */
class Theme
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
    /** @Column(type="boolean") */
    protected $current;    
    /**
     * @OneToMany(targetEntity="Templater\Model\Mapper\Layout", mappedBy="theme")
     */
    protected $layouts;
    
    public function getId()     
    {
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

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($current)
    {
        $this->current = $current;
    }

    public function getLayouts()
    {
        return $this->layouts;
    }

    public function setLayouts($layouts)
    {
        $this->layouts = $layouts;
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

