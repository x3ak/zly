<?php
/**
 * Description
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */

class Eavtype_Form_DeleteAttributes extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $tree = new Slys_Form_Element_Tree('used_attr_id');
        $tree->setValueKey('attr_id')
            ->setTitleKey('attr_name')
            ->setMultiple(true)
            ->addDisableCondition('inherited', new Zend_Validate_Identical(true))
            ->setLabel('Current attributes');

        $this->addElement($tree);

        $deleteFromChilds = new Zend_Form_Element_Checkbox('delete_from_childs');
        $deleteFromChilds->setLabel('Delete that attribute(s) from child types as well?')
                         ->setValue(1);

        $this->addElement($deleteFromChilds);

        $save = new Zend_Form_Element_Submit('delete');
        $save->setLabel('delete');
        $this->addElement($save);

        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'root_id');
    }

    public function populate(array $values)
    {
        if (!empty($values['id'])) {
            $usedAttributes = Eavtype_Model_AttrRelation::getInstance()->getTypeAttributes($values['id'], Doctrine_Core::HYDRATE_ARRAY_HIERARCHY);

            if (false === empty($usedAttributes))
                $this->getElement('used_attr_id')->addMultiOptions($usedAttributes);
        }

        parent::populate($values);
    }
}