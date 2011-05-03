<?php
/**
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: Edit.php 1082 2011-01-20 14:06:32Z zak $
 */

class Eavtype_Form_MoveAttributes extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $tree = new Slys_Form_Element_Tree('target_attr_id');
        $tree->setValueKey('attr_id')
            ->setTitleKey('attr_name')
            ->setLabel('Select target attribute');

        $this->addElement($tree);

        $tree = new Slys_Form_Element_Tree('source_attr_id');
        $tree->addDisableCondition('inherited', new Zend_Validate_Identical(true));
        $tree->setValueKey('attr_id')
            ->setTitleKey('attr_name')
            ->setMultiple(true)
            ->setLabel('Available attributes');

        $this->addElement($tree);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('Move');
        $this->addElement($save);

        $this->addElement('hidden', 'id');
    }

    public function populate(array $values)
    {
        if (!empty($values['id'])) {
            $usedAttributes = Eavtype_Model_AttrRelation::getInstance()->getTypeAttributes($values['id'], Doctrine_Core::HYDRATE_ARRAY_HIERARCHY);

            $rootRelation = Eavtype_Model_AttrRelation::getInstance()->getRootRelation($values['id']);

//            Zend_Debug::dump($rootRelation->toArray());

            if (false === empty($usedAttributes)) {

                $treeData = array(
                            array(
                                  'attr_id' => $rootRelation->attr_id,
                                  'attr_name' => 'First level'
                            )
                        );

                $usedAttributes = array_merge($treeData,$usedAttributes);

                $this->getElement('target_attr_id')->addMultiOptions($usedAttributes);
                $this->getElement('source_attr_id')->addMultiOptions($usedAttributes);
            }
        }

        parent::populate($values);
    }
}