<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 761 2010-12-14 11:49:54Z deeper $
 * @license New BSD
 */
namespace User\Model\Mapper;
/**
 * @Entity(repositoryClass="User\Model\DbTable\Rule")
 * @Table(name="user_rules")
 */
class Rule
{
   /**
    * @Id @Column(type="integer")
    * @GeneratedValue
    */
    protected $id;

    /** @Column(type="integer") */    
    protected $role_id;

    /** @Column(length=255) */
    protected $resource_id;

    /** @Column(length=50) */
    protected $rule;
    
    /**
     * @ManyToOne(targetEntity="User\Model\Mapper\Role", inversedBy="rules")
     * @JoinColumn(name="role_id", referencedColumnName="id")
     */
    protected $role;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRoleId() {
        return $this->role_id;
    }

    public function setRoleId($role_id) {
        $this->role_id = $role_id;
    }

    public function getResourceId() {
        return $this->resource_id;
    }

    public function setResourceId($resource_id) {
        $this->resource_id = $resource_id;
    }

    public function getRule() {
        return $this->rule;
    }

    public function setRule($rule) {
        $this->rule = $rule;
    }
    
    public function setRole($role) {
        $this->role = $role;
    }
}

