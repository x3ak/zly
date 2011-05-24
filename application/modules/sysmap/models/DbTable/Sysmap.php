<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Generator.php 761 2010-12-14 11:49:54Z deeper $
 * @license New BSD
 */

namespace Sysmap\Model\DbTable;

class Sysmap
{
    protected static $_instance = null;
       
    /**
     *
     * @var \Zend\Cache\Frontend\Core
     */
    protected $_cache = null;
    /**
     *
     * @var \DOMDocument
     */
    protected $_sysmap = null;
    
    protected function __construct()
    {
        $cache = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getBroker()
                    ->load('cachemanager')->getCacheManager();
        
        if($cache->hasCache('sysmap')) {
            $this->_cache = $cache->getCache('sysmap');
            if($this->_cache->test('sysmap')) {
                $this->_sysmap = $this->_cache->load('sysmap');
            }
            
        } else {
            throw 'Sysmap module require own cache';
        }

    }

    /**
     * Returns an instance of this class.
     *
     * @return Sysmap_Model_DbTable_Sysmap
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    
}

