<?php
class Eaventity_Form_EditParams extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $typeId = new Zend_Form_Element_Select('type');
        $typeId->setRequired()->setAllowEmpty(false);
        $typeId->setLabel('Select type');
        $types = Eavtype_Model_Type::getInstance()->getList(Doctrine_Core::HYDRATE_ARRAY);
        foreach($types as $type) {
            $typeId->addMultiOption($type['id'], $type['name']);
        }

        $this->addElement($typeId);
    }
}