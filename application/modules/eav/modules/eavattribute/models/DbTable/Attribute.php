<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Attribute.php 1133 2011-01-28 14:23:33Z zak $
 * @license New BSD
 */
class Eavattribute_Model_DbTable_Attribute extends Doctrine_Table
{
    const STATUS_DEFAULT = 'DEFAULT';
    const STATUS_READONLY = 'READONLY';
    const STATUS_HIDDEN = 'HIDDEN';

    const DATA_TYPE_BLOB = 'BLOB';
    const DATA_TYPE_DECIMAL = 'DECIMAL';
    const DATA_TYPE_INTEGER = 'INTEGER';
    const DATA_TYPE_TEXT = 'TEXT';
    const DATA_TYPE_TIMESTAMP = 'TIMESTAMP';
    const DATA_TYPE_STRING = 'STRING';
    const DATA_TYPE_ENTITY = 'ENTITY';

    /**
     * Returns an instance of this class.
     *
     * @return Eavattribute_Model_DbTable_Attribute
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Eavattribute_Model_Mapper_Attribute');
    }

    /**
     * @param array $params         prepared statement params (if any)
     * @param int $hydrationMode    Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Collection|array
     */
    public function getList($params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Eavattribute_Model_Mapper_Attribute')
            ->execute($params, $hydrationMode);
    }


}

