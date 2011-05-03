<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Entity.php 1070 2011-01-20 12:45:49Z zak $
 * @license New BSD
 */
class Eaventity_Model_DbTable_Entity extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     * 
     * @return Eaventity_Model_DbTable_Entity
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Eaventity_Model_Mapper_Entity');
    }


}

