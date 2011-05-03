<?php
/**
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: Edit.php 1082 2011-01-20 14:06:32Z zak $
 */

class Eavtype_Form_Edit extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $columns = Eavtype_Model_DbTable_Type::getInstance()->getColumns();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('name');
        $name->setAttrib('maxlength', $columns['name']['length']);
        $name->setAllowEmpty(false);
        $name->setRequired();
        $name->addValidator('StringLength', array('max' => $columns['name']['length']));
        $this->addElement($name);

        $tree = new Slys_Form_Element_Tree('parent_rel_id');
        $tree->setRequired(true);
        $tree->setValueKey('rel_id')
            ->setTitleKey('attr_name')
            ->setLabel('Current attributes');

        $this->addElement($tree);

        $tree = new Slys_Form_Element_Tree('available_attr_id');

        $tree->setValueKey('id')
            ->setTitleKey('name')
            ->setMultiple(true)

            ->setLabel('Available attributes');

        $this->addElement($tree);

        $id = new Zend_Form_Element_Hidden('id');
        $id->clearDecorators();
        $id->addDecorator('ViewHelper');
        $this->addElement($id);

        $rootId = new Zend_Form_Element_Hidden('root_id');
        $rootId->clearDecorators();
        $rootId->addDecorator('ViewHelper');
        $this->addElement($rootId);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $save->setOrder(999);
        $this->addElement($save);
    }

    public function populate(array $values)
    {
        if (!empty($values['id'])) {
            $usedAttributes = Eavtype_Model_AttrRelation::getInstance()
                            ->getTypeAttributes($values['id'], Doctrine_Core::HYDRATE_ARRAY_HIERARCHY);

            $rootRelation = Eavtype_Model_AttrRelation::getInstance()->getRootRelation($values['id']);

            $treeData = array(
                            array(
                                  'rel_id' => $rootRelation->id,
                                  'attr_name' => 'Add new attributes'
                            )
                        );

            $entityAttribute = Eavattribute_Model_Attribute::getInstance()->getEntityAttribute();

            if (false === empty($usedAttributes)) {
                if($entityAttribute !== false) {
                    foreach($usedAttributes as $k => $attribute) {

                        if($attribute['attr_data_type'] == 'ENTITY' and $attribute['attr_id'] !== $entityAttribute->id){
                            $typeId = $attribute['attr_name'];
                            $entityType = Eavtype_Model_Type::getInstance()->findById($typeId);
                            $usedAttributes[$k]['attr_name'] = $entityType->name.'(Entity)';
                        }
                    }
                }
                $this->getElement('parent_rel_id')->addMultiOptions(array_merge($treeData, $usedAttributes));
            } else {
                $this->getElement('parent_rel_id')->addMultiOptions($treeData);
            }

            $this->getElement('parent_rel_id')->setValue($rootRelation->id);

            $availableAttributes = Eavattribute_Model_Attribute::getInstance()->getAvailableFor($values['id']);

            if(false === empty($availableAttributes) ) {
                $availableAttributes = $availableAttributes->toArray();

                foreach($availableAttributes as $k => $attribute) {
                    if($attribute['data_type'] == 'ENTITY' and $attribute['id'] !== $entityAttribute->id){
                        $typeId = $attribute['name'];
                        $entityType = Eavtype_Model_Type::getInstance()->findById($typeId);
                        $availableAttributes[$k]['name'] = $entityType->name.'(Entity)';
                    }
                }
            }

            $this->getElement('available_attr_id')->addMultiOptions($availableAttributes);
        }

        parent::populate($values);
    }

    public function addSelectEntityType()
    {
        $select = new Zend_Form_Element_Select('entity_type_id');
        $select->setLabel('select_entity_type');
        $select->setAllowEmpty(false);
        $select->setRequired(true);

        $select->addMultiOption('','');

        $list = Eavtype_Model_Type::getInstance()->getList(Doctrine_Core::HYDRATE_ARRAY);
        foreach($list as $item) {
            if($item['id'] == $this->getElement('id')->getValue())
                continue;
            $select->addMultiOption($item['id'],$item['name']);
        }

        $this->addElement($select);
    }
}