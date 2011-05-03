<?php
/**
 * User: Pavel
 * Date: 25.01.11
 * Time: 18:00
 */
class Eavtype_Model_AttrRelation
{
    /** @var Eavtype_Model_AttrRelation */
    private static $_instance;

    protected function __construct()
    {}

    /**
     * @static
     * @return Eavtype_Model_AttrRelation
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * @param  $typeId
     * @param array $params         Query parameters
     * @param int $hydrationMode    Hydration mode: see Doctrine_Core::HYDRATE_* constants
     * @return Eavtype_Model_Mapper_AttrRelation|mixed
     */
    public function getRootRelation($typeId, $params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Eavtype_Model_Mapper_AttrRelation')
            ->where('type_id = ?',$typeId)
            ->andWhere('level = 0')->fetchOne($params, $hydrationMode);
    }

    /**
     * @param int $typeId
     * @return Eavtype_Model_Mapper_AttrRelation
     */
    public function createRootRelation($typeId)
    {
        $mapper = $this->getRootRelation($typeId);

        if($mapper !== false) {
            return $mapper;
        }

        $rootAttribute = Eavattribute_Model_DbTable_Attribute::getInstance()->findOneBy('system_name','root_attribute');

        $mapper = new Eavtype_Model_Mapper_AttrRelation();
        $mapper->type_id = $typeId;
        $mapper->attr_id = $rootAttribute->id;
        $mapper->save();

        Eavtype_Model_DbTable_AttrRelation::getInstance()->getTree()->createRoot($mapper);

        return $mapper;
    }

    public function moveAttributes($attrIds, $targetAttrId, $typeId)
    {
        if(false === is_array($attrIds)) {
            $attrIds = array($attrIds);
        }

        $target = Doctrine_Query::create()
            ->select()
            ->from('Eavtype_Model_Mapper_AttrRelation')
            ->where('type_id = ?', $typeId)
            ->andWhere('attr_id = ?', $targetAttrId)->fetchOne();

        $descendants = Eavtype_Model_Type::getInstance()->findById($typeId)->getNode()->getDescendants();

        foreach($attrIds as $attrId) {
            if(false === ($attrId instanceof Eavtype_Model_Mapper_AttrRelation)) {
                $source = Doctrine_Query::create()
                            ->select()
                            ->from('Eavtype_Model_Mapper_AttrRelation')
                            ->where('type_id = ?', $typeId)
                            ->andWhere('attr_id = ?', $attrId)->fetchOne();
            } else {
                $source = $attrId;
            }


            if($source->type_id != $typeId)
                continue;

            if($target->rgt > $source->lft && $target->rgt < $source->rgt)
                continue;

            $source->getNode()->moveAsLastChildOf($target);
        }

        if(false !== $descendants) {
            foreach($descendants as $node) {
                $this->moveAttributes($attrIds,$targetAttrId,$node->id);
            }
        }
    }

    /**
     * @param int $typeId
     * @param int $hydrationMode            Doctrine_Core::*
     * @return array|mixed                  Depends on $hydrationMode
     */
    public function getTypeAttributes($typeId, $hydrationMode = null)
    {
        $dql = Doctrine_Query::create()
            ->select('tar.inherited, tar.id as rel_id, attr.id as attr_id, attr.name as attr_name, attr.data_type as attr_data_type')
            ->from('Eavtype_Model_Mapper_AttrRelation tar')
            ->leftJoin('tar.Attr attr')
            ->where('tar.level > 0');


        $treeObject = Eavtype_Model_DbTable_AttrRelation::getInstance()->getTree();
        $treeObject->setBaseQuery($dql);
        return $treeObject->fetchTree(array('root_id' => $typeId), $hydrationMode);
    }
}