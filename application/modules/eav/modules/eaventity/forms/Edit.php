<?php
/**
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: Edit.php 1082 2011-01-20 14:06:32Z zak $
 */

class Eaventity_Form_Edit extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $columns = Eaventity_Model_DbTable_Entity::getInstance()->getColumns();

        $name = new Zend_Form_Element_Text('entity_name');
        $name->setLabel('entity_name');
        $name->setAttrib('maxlength',$columns['entity_name']['length']);
        $name->setAllowEmpty(false);
        $name->setRequired();
        $name->addValidator('StringLength',array('max'=>$columns['entity_name']['length']));
        $this->addElement($name);

        $typeId = new Zend_Form_Element_Hidden('type_id');
        $typeId->clearDecorators();
        $typeId->addDecorator('ViewHelper');
        $this->addElement($typeId);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $save->setOrder(9999);
        $this->addElement($save);
    }

    /**
     * @param int $typeId
     * @return void
     */
    public function prepareForType($typeId, Eaventity_Model_Mapper_Entity $entity = null)
    {
        $typeAttributes = Eavtype_Model_AttrRelation::getInstance()->getTypeAttributes($typeId);

        $this->getElement('type_id')->setValue($typeId);

        if(empty($typeAttributes)) {
            return;
        }

        $valuesSubForm = new Zend_Form_SubForm();
        $valuesSubForm->removeDecorator('Fieldset');
        $valuesSubForm->removeDecorator('DtDdWrapper');

        /** @var $relation Eavtype_Model_Mapper_AttrRelation */
        foreach($typeAttributes as $k => $relation)
        {
            $indexSubForm = new Zend_Form_SubForm();

            $indexSubForm->removeDecorator('Fieldset');
            $indexSubForm->removeDecorator('DtDdWrapper');


            $attrId = new Zend_Form_Element_Hidden('attr_id');
            $attrId->setValue($relation->attr_id);
            $attrId->clearDecorators();
            $attrId->addDecorator('ViewHelper');
            $indexSubForm->addElement($attrId);

            $relationId = new Zend_Form_Element_Hidden('type_attr_relation_id');
            $relationId->setValue($relation->id);
            $relationId->clearDecorators();
            $relationId->addDecorator('ViewHelper');
            $indexSubForm->addElement($relationId);


            $this->_addAttributeToSubForm($relation, $indexSubForm, $entity);


            $valuesSubForm->addSubForm($indexSubForm, $k);
        }
        $this->addSubForm($valuesSubForm, 'EntityValues');
    }

    /**
     * @param Eavtype_Model_Mapper_AttrRelation $relation
     * @return void
     */
    private function _addAttributeToSubForm(Eavtype_Model_Mapper_AttrRelation $relation, Zend_Form_SubForm $indexSubForm, Eaventity_Model_Mapper_Entity $entityMapper = null)
    {

        switch($relation->Attr->data_type) {
            case Eavattribute_Model_DbTable_Attribute::DATA_TYPE_STRING:
                $value = new Zend_Form_Element_Text("string_value");
                $value->setLabel($relation->Attr->name);
                $value->setRequired($relation->required);
                $indexSubForm->addElement($value);
                break;

            case Eavattribute_Model_DbTable_Attribute::DATA_TYPE_INTEGER:
                $value = new Zend_Form_Element_Text("integer_value");
                $value->setLabel($relation->Attr->name);
                $value->setRequired($relation->required);
                $value->addValidator('Int');
                $indexSubForm->addElement($value);
                break;

            case Eavattribute_Model_DbTable_Attribute::DATA_TYPE_DECIMAL:
                $value = new Zend_Form_Element_Text("decimal_value");
                $value->setLabel($relation->Attr->name);
                $value->addValidator('Float');
                $value->setRequired($relation->required);
                $indexSubForm->addElement($value);
                break;

            case Eavattribute_Model_DbTable_Attribute::DATA_TYPE_TEXT:
                $value = new Zend_Form_Element_Textarea("text_value");
                $value->setLabel($relation->Attr->name);
                $value->setRequired($relation->required);
                $indexSubForm->addElement($value);
                break;

            case Eavattribute_Model_DbTable_Attribute::DATA_TYPE_ENTITY:
                $value = new Zend_Form_Element_Select("related_entity_id");
                $typeMapper = Eavtype_Model_Type::getInstance()->findById($relation->Attr->name);
                $value->setLabel($relation->Attr->name);
                $value->setRequired(false);


                $value->setLabel($typeMapper->name);
                $descendants = $typeMapper->getNode()->getDescendants();

                $excludeEntityIds = new ArrayObject();
                if(false === empty($entityMapper)) { //edit case, exclude entities that related to this entity
                    $value->setValue($entityMapper->id);

                    $excludeEntityIds[] = $entityMapper->id;
                    $this->_findRelatedEntities($entityMapper, $excludeEntityIds);
                }

                $typeIds = array($typeMapper->id);
                /** @var $descendant Eavtype_Model_Mapper_Type */
                if($descendants !== false) {
                    foreach($descendants as $descendant) {
                        $typeIds[] = $descendant->id;
                    }
                }

                $entities = Eaventity_Model_Entity::getInstance()->findByType($typeIds);

                if(false === empty($entities)) {
                    foreach($entities as $entity) {
                        if(in_array($entity['id'], (array)$excludeEntityIds))
                            continue;

                        $value->addMultiOption($entity['id'], $entity['name']);
                        $value->setRequired($relation->required);
                    }

                }

                $indexSubForm->addElement($value);

                break;
            case Eavattribute_Model_DbTable_Attribute::DATA_TYPE_BLOB:
            case Eavattribute_Model_DbTable_Attribute::DATA_TYPE_TIMESTAMP:
            default:
                throw new Zend_Exception("Unsupported attribute data type: '".$relation->Attr->data_type."'!", E_USER_WARNING);
        }

    }

    private function _findRelatedEntities(Eaventity_Model_Mapper_Entity $entity, ArrayObject $ret)
    {
        /** @var $entityValue Eaventity_Model_Mapper_Value */
        foreach($entity->RelatedTo as $entityValue) {
            $ret[] = $entityValue->related_entity_id;
            $this->_findRelatedEntities($entityValue->RelatedEntity, $ret);
        }
    }
}