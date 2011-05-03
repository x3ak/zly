<?php
/**
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: Edit.php 1082 2011-01-20 14:06:32Z zak $
 */

class Eavtype_Form_AddAttribute extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $columns = Eavtype_Model_DbTable_Type::getInstance()->getColumns();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('name');
        $name->setAttrib('maxlength',$columns['name']['length']);
        $name->setAllowEmpty(false);
        $name->setRequired();
        $name->addValidator('StringLength',array('max'=>$columns['name']['length']));
        $this->addElement($name);

        $save = new Zend_Form_Element_Submit('save');
        $save->setLabel('save');
        $this->addElement($save);


    }

    public function setType(Eavtype_Model_Mapper_Type $type)
    {
        $availableAttributes = Eavattribute_Model_Attribute::getInstance()->getAvailableFor($type);

        /** @var $attribute Eavattribute_Model_Mapper_Attribute */
        foreach($availableAttributes as $attribute)
        {
            $attributeElement = new Zend_Form_Element_Select('attr_id');
            $attributeElement->addMultiOption($attribute->id, $attribute->name);
            $this->addElement($attributeElement);
        }

    }

}