<?php
/**
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: Edit.php 1141 2011-01-28 23:23:35Z zak $
 */

class Eavattribute_Form_Edit extends Zend_Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $columns = Eavattribute_Model_DbTable_Attribute::getInstance()->getColumns();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('name');
        $name->setAttrib('maxlength',$columns['name']['length']);
        $name->setAllowEmpty(false);
        $name->setRequired();
        $name->addValidator('StringLength',array('max'=>$columns['name']['length']));
        $this->addElement($name);

        $sysnameRegexValidator = new Zend_Validate_Regex('/^([0-9a-z\_\-]+)$/');
        $sysnameRegexValidator->setMessage('error_message_invalid_sysname', Zend_Validate_Regex::NOT_MATCH);

        $sysname = new Zend_Form_Element_Text('system_name');
        $sysname->setLabel('system_name');
        $sysname->addValidator($sysnameRegexValidator);
        $sysname->setAttrib('maxlength',$columns['system_name']['length']);
        $sysname->setAllowEmpty(false);
        $sysname->setRequired();
        $sysname->addValidator('StringLength',array('max'=>$columns['system_name']['length']));
        $sysname->setDescription('System name should be unique, and contain only digits, letters, underscore and dash.');
        $this->addElement($sysname);


        $dataType = new Zend_Form_Element_Select('data_type');
        $dataType->setLabel('data_type');

        foreach($columns['data_type']['values'] as $value) {
            if(strtoupper($value) == Eavattribute_Model_DbTable_Attribute::DATA_TYPE_ENTITY)
                continue;

            $dataType->addMultiOption($value,$value);
        }

        $this->addElement($dataType);
        $save = new Zend_Form_Element_Submit('save');

        $save->setLabel('save');
        $this->addElement($save);

    }

}