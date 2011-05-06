<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Role.php 1232 2011-04-17 21:00:36Z deeper $
 * @license New BSD
 */

/**
 * @Entity(repositoryClass="User\Model\DbTable\Role")
 * @Table(name="user_roles")
 */

namespace User\Model\Mapper;

class Role
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /** @Column(length=50) */
    protected $name;

    /** @Column(type="integer") */
    protected $parent_id;

    /** @Column(type="boolean") */
    protected $is_default;
    
    /**
     * @OneToMany(targetEntity="User\Model\Mapper\Rule", mappedBy="role_id")
     */
    protected $rules;
   
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getParent_id() {
        return $this->parent_id;
    }

    public function setParent_id($parent_id) {
        $this->parent_id = $parent_id;
    }

    public function getIs_default() {
        return $this->is_default;
    }

    public function setIs_default($is_default) {
        $this->is_default = $is_default;
    }

    public function getRules() {
        return $this->rules;
    }

    public function setRules($rules) {
        $this->rules = $rules;
    }
}

