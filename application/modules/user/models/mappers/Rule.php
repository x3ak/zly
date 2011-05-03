<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 761 2010-12-14 11:49:54Z deeper $
 * @license New BSD
 */

/**
 * @Entity
 * @Table(name="user_rules")
 */
class User_Model_Mapper_Rule
{
   /**
    * @Id @Column(type="integer")
    * @GeneratedValue
    */
    protected $id;

    /** @Column(type="integer") */    
    protected $role_id;

    /** @Column(type="integer") */
    protected $resource_id;

    /** @Column(length=50) */
    protected $rule;
    
    /**
     * @ManyToOne(targetEntity="User_Model_Mapper_Role", inversedBy="rules")
     * @JoinColumn(name="role_id", referencedColumnName="id")
     */
    protected $role;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRole_id() {
        return $this->role_id;
    }

    public function setRole_id($role_id) {
        $this->role_id = $role_id;
    }

    public function getResource_id() {
        return $this->resource_id;
    }

    public function setResource_id($resource_id) {
        $this->resource_id = $resource_id;
    }

    public function getRule() {
        return $this->rule;
    }

    public function setRule($rule) {
        $this->rule = $rule;
    }
}

