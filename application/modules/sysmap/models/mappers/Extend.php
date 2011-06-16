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

    /** @Column(length=50) */
    protected $name;
    
    /** @Column(length=50) */
    protected $hash;

    /** @Column(length=50) */
    protected $actionhash;

    /** @Column(length=50) */
    protected $qualifier;
    
    /** @array */
    protected $params;
}

