<?php
class Eavattribute_Model_Attribute
{
    /** @var Eavattribute_Model_Attribute */
    private static $_instance;
    /**
     * @static
     * @return Eavattribute_Model_Attribute
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    //closing constructor
    protected function __construct() {}
    /**
     * @return array|Doctrine_Collection
     */
    public function getList()
    {
        return Eavattribute_Model_DbTable_Attribute::getInstance()->getList();
    }

    /**
     * Searches for attribute with given id
     *
     * @param int $attributeId
     * @return Eavattribute_Model_Mapper_Attribute
     */
    public function findById($attributeId)
    {
        if(empty($attributeId)) return false;
        return Eavattribute_Model_DbTable_Attribute::getInstance()->findOneBy('id', $attributeId);
    }

    /**
     * Searches for attribute with given sysname
     *
     * @param string $sysname
     * @return Eavattribute_Model_Mapper_Attribute
     */
    public function findBySysname($sysname)
    {
        if(empty($sysname)) return false;
        return Eavattribute_Model_DbTable_Attribute::getInstance()->findOneBy('system_name', $sysname);
    }


    public function getUsedBy($typeId)
    {
        if($typeId instanceof Eavtype_Model_Mapper_Type) {
            $typeMapper = $typeId;
        } else {
            $typeMapper = Eavtype_Model_Type::getInstance()->findById($typeId);
        }

        /** @var $ancestorTypes Doctrine_Collection */
        $ancestorTypes = $typeMapper->getNode()->getAncestors();

        $ancestorsIds = array();

        if (empty($ancestorTypes) === false) {
            /** @var $ancestor Eavtype_Model_Mapper_Type */
            foreach($ancestorTypes as $k => $ancestor) {
                $ancestorsIds[] = $ancestor->id;
            }
        }

        $ancestorsIds[] = $typeMapper->id;

        $dql = Doctrine_Query::create()
                ->select()
                ->from('Eavattribute_Model_Mapper_Attribute t')
                ->innerJoin('t.TypeAttrRelations tar')
                ->whereIn('tar.type_id',$ancestorsIds);

        return $dql->execute();
    }


    public function getAvailableFor($typeId)
    {
        if($typeId instanceof Eavtype_Model_Mapper_Type) {
            $typeMapper = $typeId;
        } else {
            $typeMapper = Eavtype_Model_Type::getInstance()->findById($typeId);
        }

        /** @var $ancestorTypes Doctrine_Collection */
        $ancestorTypes = $typeMapper->getNode()->getAncestors();

        $ancestorsIds = array();

        if (empty($ancestorTypes) === false) {
            /** @var $ancestor Eavtype_Model_Mapper_Type */
            foreach($ancestorTypes as $k => $ancestor) {
                $ancestorsIds[] = $ancestor->id;
            }
        }

        $ancestorsIds[] = $typeMapper->id;

        $dql = Doctrine_Query::create()
            ->select()
            ->from('Eavattribute_Model_Mapper_Attribute t')
            ->leftJoin('t.TypeAttrRelations tar WITH tar.type_id IN ('.implode(',',$ancestorsIds).')')
            ->where('tar.id IS NULL')
            ->andWhere('t.status != ?', Eavattribute_Model_DbTable_Attribute::STATUS_HIDDEN);


        return $dql->execute();
    }

    /**
     * @param int $typeId
     * @return Eavattribute_Model_Mapper_Attribute
     */
    public function createEntityAttribute($typeId) {
        $attribute = new Eavattribute_Model_Mapper_Attribute();
        $attribute->name = $typeId;
        $attribute->system_name = $typeId.":".microtime(true);
        $attribute->data_type = Eavattribute_Model_DbTable_Attribute::DATA_TYPE_ENTITY;
        $attribute->status = Eavattribute_Model_DbTable_Attribute::STATUS_HIDDEN;
        $attribute->save();
        return $attribute;
    }

    /**
     * @return Eavattribute_Model_Mapper_Attribute
     */
    public function getEntityAttribute() {
        return Doctrine_Query::create()
            ->select()
            ->from('Eavattribute_Model_Mapper_Attribute')
            ->where('system_name = ?', 'entity')->fetchOne();

    }
}