<?php
/**
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: EditAttributes.php 1102 2011-01-25 08:45:41Z criolit $
 */

class Eavtype_Form_EditAttributes extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $tree = new Slys_Form_Element_Tree('current_attr_id');
        $tree->setValueKey('id')
             ->setTitleKey('name')
             ->setMultiple(true)
             ->setLabel('Current attributes');

        $this->addElement($tree);

        $save = new Zend_Form_Element_Submit('delete');
        $save->setLabel('delete_selected_attributes');
        $this->addElement($save);

        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'root_id');
    }

    public function populate(array $values)
    {
        if (!empty($values['id'])) {
            $usedAttributes = Eavattribute_Model_Attribute::getInstance()->getUsedBy($values['id']);
            $this->getElement('current_attr_id')->addMultiOptions($usedAttributes->toArray());
        }

        parent::populate($values);
    }
}