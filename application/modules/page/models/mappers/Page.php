<?php

namespace Page\Model\Mapper;

/**
 * @Entity
 * @Table(name="page_pages")
 */
class Page
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /** @Column(length=64) */
    protected $sysname;
    /** @Column(length=65) */
    protected $title;
    /** @Column(length=4000) */
    protected $content;    
    /** @Column(length=255) */
    protected $meta_keywords;
    /** @Column(length=4000) */
    protected $meta_description;
    /** @Column(type="boolean") */
    protected $built_in;
    /** @Column(type="boolean") */
    protected $published;
    /** @Column(type="integer") */    
    protected $ordering;
    
    public function getId()     
    {
        return $this->id;
    }
    
    public function getSysname()     
    {
        return $this->sysname;
    }

    public function setSysname($sysname)
    {
        $this->sysname = $sysname;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;
    }

    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;
    }

    public function getBuiltIn()
    {
        return $this->built_in;
    }

    public function setBuiltIn($built_in)
    {
        $this->built_in = $built_in;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        $this->published = $published;
    }

    public function getOrdering()
    {
        return $this->ordering;
    }

    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
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

