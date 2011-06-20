<?php

/**
 * SlyS
 * 
 * @version $Id: Role.php 1232 2011-04-17 21:00:36Z deeper $
 * @license New BSD
 */

/**
 * @Entity
 * @Table(name="sysmap_extends")
 */

namespace Sysmap\Model\Mapper;

class Extend
{
    /**
     * @Id 
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    
    /** 
     * @Column(length=50) 
     */
    protected $hash;

    /** @Column(length=50) */
    protected $name;   

    /** @Column(length=50) */
    protected $actionhash;
    
    /** @Column(length=1000) */
    protected $params;
    
    public function getId()     
    {
        return $this->id;
    }
    
    public function getHash()     
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getActionhash()
    {
        return $this->actionhash;
    }

    public function setActionhash($actionhash)
    {
        $this->actionhash = $actionhash;
    }

    public function getParams()
    {
        if(empty($this->params))
                return array();
        
        return unserialize($this->params);
    }

    public function setParams($params)
    {
        $this->params = serialize($params);
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'hash' => $this->getHash(),
            'name' => $this->getName(),
            'actionhash' => $this->getActionhash(),
            'params' => $this->getParams()            
        );
    }
}

