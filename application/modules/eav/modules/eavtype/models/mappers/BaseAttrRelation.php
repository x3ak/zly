<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Eavtype_Model_Mapper_BaseAttrRelation', 'doctrine');

/**
 * Eavtype_Model_Mapper_BaseAttrRelation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $type_id
 * @property integer $attr_id
 * @property boolean $searchable
 * @property boolean $required
 * @property boolean $inherited
 * @property Eavtype_Model_Mapper_Type $Type
 * @property Eavattribute_Model_Mapper_Attribute $Attr
 * @property Doctrine_Collection $EntityValues
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: BaseAttrRelation.php 1224 2011-04-04 13:58:41Z deeper $
 */
class Eavtype_Model_Mapper_BaseAttrRelation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('eav_type_attr_relations');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('type_id', 'integer', 11, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '11',
             ));
        $this->hasColumn('attr_id', 'integer', 11, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '11',
             ));
        $this->hasColumn('searchable', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('required', 'boolean', null, array(
             'type' => 'boolean',
             'default' => true,
             ));
        $this->hasColumn('inherited', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Eavtype_Model_Mapper_Type as Type', array(
             'local' => 'type_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Eavattribute_Model_Mapper_Attribute as Attr', array(
             'local' => 'attr_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('Eaventity_Model_Mapper_Value as EntityValues', array(
             'local' => 'id',
             'foreign' => 'type_attr_relation_id'));

        $nestedset0 = new Doctrine_Template_NestedSet(array(
             'hasManyRoots' => true,
             'rootColumnName' => 'type_id',
             ));
        $this->actAs($nestedset0);
    }
}