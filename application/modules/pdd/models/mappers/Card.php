<?php

namespace Pdd\Model\Mapper;

/**
 * @Entity
 * @Table(name="pdd_cards")
 */
class Card 
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /** @Column(length=50) */
    protected $title;

    /** @Column(type="integer", nullable=true) */
    protected $category_id;
    
    /** @Column(type="integer", nullable=true) */
    protected $answer;
    
    /** @Column(length=255, nullable=true) */
    protected $picture;
    
    /**
     * @OneToMany(targetEntity="Pdd\Model\Mapper\Question", mappedBy="card")
     */
    protected $questions;
    
    /**
     * @ManyToOne(targetEntity="Pdd\Model\Mapper\Category")
     * @JoinColumn(name="category_id", referencedColumnName="id", unique=false)
     */
    protected $category;


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

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function setQuestions($questions)
    {
        $this->questions = $questions;
    }
    
    public function getCategory()     
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
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