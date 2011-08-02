<?php

namespace Pdd\Model\Mapper;

/**
 * @Entity
 * @Table(name="pdd_questions")
 */
class Question 
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /** @Column(length=255) */
    protected $text;
    
    /** @Column(length=50) */
    protected $ordering;
    
    /** @Column(type="integer", nullable=true) */
    protected $card_id;
    
    /**
     * @ManyToOne(targetEntity="Pdd\Model\Mapper\Card", inversedBy="questions")
     * @JoinColumn(name="card_id", referencedColumnName="id")
     */
    protected $card;
}