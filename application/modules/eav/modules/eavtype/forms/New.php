<?php
/**
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: New.php 1105 2011-01-25 15:01:50Z zak $
 */

class Eavtype_Form_New extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $treeData = array(
            array(
                'id' => '0',
                'name' => 'New'
            )
        );

        $treeData = array_merge($treeData, Eavtype_Model_Type::getInstance()->getFullTree());

        $tree = new Slys_Form_Element_Tree('parent_id');
        $tree->setValueKey('id')
             ->setTitleKey('name')
             ->setLabel('Base type')
             ->addMultiOptions($treeData);

        $this->addElement($tree);

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

}