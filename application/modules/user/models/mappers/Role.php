<?php

/**
 * Zly
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

    /** @Column(type="integer", nullable=true) */
    protected $parent_id;

    /** @Column(type="boolean", nullable=true) */
    protected $is_default;
    
    /**
     * @OneToMany(targetEntity="User\Model\Mapper\Rule", mappedBy="role")
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

    public function getParentId() {
        return $this->parent_id;
    }

    public function setParentId($parent_id) {
        $this->parent_id = $parent_id;
    }

    public function getIsDefault() {
        return $this->is_default;
    }

    public function setIsDefault($is_default) {
        $this->is_default = $is_default;
    }

    public function getRules() {
        return $this->rules;
    }

    public function setRules($rules) {
        $this->rules = $rules;
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

