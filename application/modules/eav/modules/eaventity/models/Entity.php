<?php
class Eaventity_Model_Entity
{
    /** @var Eaventity_Model_Entity */
    private static $_instance;

    protected function __construct()
    {}

    /**
     * @static
     * @return Eaventity_Model_Entity
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * Searches for entity with given id
     *
     * @param int $typeId
     * @return false|Eaventity_Model_Mapper_Entity
     */
    public function findById($entityId)
    {
        if(empty($entityId))
            return false;

        return Eaventity_Model_DbTable_Entity::getInstance()->findOneBy('id', $entityId);
    }

    /**
     * Returns list of entities
     *
     * @param int $hydrationMode            Doctrine_Core::*
     * @return Doctrine_Collection
     */
    public function getList()
    {
        $result = Doctrine_Query::create()
            ->select()
            ->from('Eaventity_Model_Mapper_Entity e')
            ->leftJoin('e.EntityValues ev')
            ->leftJoin('e.Type t')
            ->leftJoin('ev.Attr a')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $return = array();
        foreach($result as $entity) {
            $return[] = $this->normalizeValues($entity);
        }

        return $return;
    }

    /**
     * @param array|Eaventity_Model_Mapper_Entity $entityData
     * @return array
     */
    public function normalizeValues($entityData) {
        $result = array();

        if($entityData instanceof Eaventity_Model_Mapper_Entity) {
            foreach($entityData->EntityValues as $value) {
                $result['id'] = $value->id;
                $result['Type'] = $entityData->Type;
                $result[$value->Attr->system_name] = $value[strtolower($value->Attr->data_type).'_value'];
            }
        } else {
            $result['id'] = $entityData['id'];
            $result['Type'] = $entityData['Type'];
            $result['entity_name'] = $entityData['entity_name'];

            foreach($entityData['EntityValues'] as $value) {
                $result[$value['Attr']['system_name']] = $value[strtolower($value['Attr']['data_type']).'_value'];
            }
        }

        return $result;
    }

    /**
     * @param int|array|Doctrine_Collection $typeId
     * @return array
     */
    public function findByType($typeId)
    {
        $types = array();
        if($typeId instanceof Doctrine_Collection) {
            /** @var $typeMapper Eavtype_Model_Mapper_Type */
            foreach($typeId as $typeMapper) {
                $types[] = $typeMapper->id;
            }
        }
        elseif( is_array($typeId) ) {
            $types = $typeId;
        } else {
            $types[] = $typeId;
        }

        $result = Doctrine_Query::create()
            ->select()
            ->from('Eaventity_Model_Mapper_Entity e')
            ->leftJoin('e.EntityValues ev')
            ->leftJoin('e.Type t')
            ->leftJoin('ev.Attr a')
            ->whereIn('e.type_id', $types)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $return = array();
        foreach($result as $entity) {
            $return[] = $this->normalizeValues($entity);
        }

        return $return;
    }




}