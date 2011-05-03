<?php

/**
 * SlyS
 *
 * This is a class generated with Zend_CodeGenerator.
 *
 * @version $Id: Type.php 1081 2011-01-20 14:04:46Z zak $
 * @license New BSD
 */
class Eavtype_Model_DbTable_Type extends Doctrine_Table
{

    /**
     * Returns an instance of this class.
     *
     * @return Eavtype_Model_DbTable_Type
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Eavtype_Model_Mapper_Type');
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
            ->from('Eavtype_Model_Mapper_Type')
            ->execute($params, $hydrationMode);
    }

}

