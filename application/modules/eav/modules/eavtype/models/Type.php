<?php
class Eavtype_Model_Type
{
    /** @var Eavtype_Model_Type */
    private static $_instance;

    protected function __construct()
    {}

    /**
     * @static
     * @return Eavtype_Model_Type
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * Searches for type with given id
     *
     * @param int $typeId
     * @return false|Eavtype_Model_Mapper_Type
     */
    public function findById($typeId)
    {
        if(empty($typeId))
            return false;

        return Eavtype_Model_DbTable_Type::getInstance()->findOneBy('id', $typeId);
    }


    /**
     * @param int $hydrationMode            Doctrine_Core::*
     * @return array|mixed                  Depends on $hydrationMode
     */
    public function getList($hydrationMode = null)
    {
        return Eavtype_Model_DbTable_Type::getInstance()->findAll($hydrationMode);;
    }

    /**
     * @return Doctrine_Tree
     */
    public function getFullTree() {
        $tree = Doctrine_Core::getTable('Eavtype_Model_Mapper_Type')->getTree();

        $rootTypes = $tree->fetchRoots();

        $hierarchy = array();

        foreach($rootTypes as $type)
            $hierarchy = array_merge($hierarchy, $tree->fetchTree(array('root_id' => $type->id), Doctrine_Core::HYDRATE_ARRAY_HIERARCHY));

        return $hierarchy;
    }

    public function createNew(array $data)
    {
        $mapper = new Eavtype_Model_Mapper_Type();
        $mapper->name = $data['name'];
        $mapper->save();

        $rootRelation = Eavtype_Model_AttrRelation::getInstance()->createRootRelation($mapper->id);

        if(false === empty($data['parent_id'])) {
            $parentType = Eavtype_Model_Type::getInstance()->findById($data['parent_id']);
            if(false !== $parentType) {
                $mapper->getNode()->insertAsLastChildOf($parentType);
            }
        }

        if( empty($parentType) ) {
            Eavtype_Model_DbTable_Type::getInstance()->getTree()->createRoot($mapper);
        } else {
            $parentAttributes = Eavtype_Model_AttrRelation::getInstance()->getTypeAttributes($parentType->id, Doctrine_Core::HYDRATE_ARRAY);

            $maxRgt = 2;

            foreach($parentAttributes as $k => $relation) {

                $newRelation = new Eavtype_Model_Mapper_AttrRelation();
                $newRelation->fromArray($relation);
                $newRelation->type_id = $mapper->id;
                $newRelation->id = null;

                $newRelation->inherited = true;
                $newRelation->save();

                if($maxRgt < $newRelation->rgt) {
                    $maxRgt = $newRelation->rgt;
                }
            }

            $rootRelation->rgt = $maxRgt+1;
            $rootRelation->save();
        }

        return $mapper;
    }

    /**
     * @param array $data
     * @return Eavtype_Model_Mapper_Type
     */
    public function doEdit(Eavtype_Model_Mapper_Type $mapper, array $data) {

        $mapper->fromArray($data);
        $mapper->save();

        // copying attributes
        if (empty($data['parent_rel_id']) === false) {
            $rootAttrib = Doctrine_Query::create()->select()
                                                ->from('Eavtype_Model_Mapper_AttrRelation')
                                                ->where('id = ?', $data['parent_rel_id'])
                                                ->fetchOne();

            if(false === empty($rootAttrib)) {
                if (empty($data['available_attr_id']) === false) {

                    $entityAttribute = Eavattribute_Model_Attribute::getInstance()->findBySysname('entity');

                    foreach($data['available_attr_id'] as $attr_id) {

                        if($entityAttribute !== false and $attr_id === $entityAttribute->id) {
                            $attribute = Eavattribute_Model_Attribute::getInstance()->createEntityAttribute($data['entity_type_id']);
                            $attr_id = $attribute->id;
                        }

                        $this->_addAttribute($mapper, $attr_id, $rootAttrib->attr_id);
                    }
                }
            }
        }

        return $mapper;
    }

    private function _addAttribute(Eavtype_Model_Mapper_Type $mapper, $attributeId,  $parentAttributeId, $inherited = false)
    {
        if( false === $mapper->exists()) {
            return false;
        }


        $existingRelation = Doctrine_Query::create()
            ->select()
            ->from('Eavtype_Model_Mapper_AttrRelation')
            ->where('type_id = ?', $mapper->id)
            ->andWhere('attr_id = ?', $attributeId)->fetchOne();

        $parentRelation = Doctrine_Query::create()
            ->select()
            ->from('Eavtype_Model_Mapper_AttrRelation')
            ->where('type_id = ?', $mapper->id)
            ->andWhere('attr_id = ?',$parentAttributeId)->fetchOne();

        if($existingRelation !== false) {
            $existingRelation->getNode()->moveAsLastChildOf($parentRelation);
            $existingRelation->inherited = $inherited;
            $existingRelation->save();

        } else {
            $newRelation = new Eavtype_Model_Mapper_AttrRelation();
            $newRelation->type_id = $mapper->id;
            $newRelation->attr_id = $attributeId;
            $newRelation->inherited = $inherited;
            $newRelation->save();

            $newRelation->getNode()->insertAsLastChildOf($parentRelation);
        }

        $childrens = $mapper->getNode()->getChildren();

        if(empty($childrens))
            return true;

        /** @var $child Eavtype_Model_Mapper_Type */
        foreach($childrens as $child) {
            $this->_addAttribute($child, $attributeId, $parentAttributeId, true);
        }

    }

    /**
     * @param  $typeId
     * @param array $attributeIds
     * @return void
     */
    public function deleteAttributes($typeId, array $attributeIds, $deleteFromChilds = true)
    {
        if (empty($attributeIds) or empty($typeId))
            return;

        $types = array($typeId);

        if ($deleteFromChilds) {
            $childs = Eavtype_Model_DbTable_Type::getInstance()->find($typeId)->getNode()->getDescendants();

            foreach($childs as $child)
                $types[] = $child->id;
        }

        $collection = Doctrine_Query::create()->select()
                                            ->from('Eavtype_Model_Mapper_AttrRelation')
                                            ->whereIn('type_id', $types)
                                            ->andWhereIn('attr_id', $attributeIds)
                                            ->execute();

        foreach($collection as $attribute)
            if (empty($attribute) === false)
                $attribute->getNode()->delete();
    }
}