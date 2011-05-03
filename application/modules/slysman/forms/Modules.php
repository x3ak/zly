<?php

class Slysman_Form_Modules extends Zend_Form
{

    public function init()
    {
        $this->setMethod('POST');
        
        
        $loginElement = new Zend_Form_Element_Select('action');
        $loginElement->setLabel('Action:');
        $loginElement->setRequired(true)->addMultiOptions(array('install'=>'Install selected'));
        $this->addElement($loginElement);

        $loginElement = new Zend_Form_Element_MultiCheckbox('modules');
        $loginElement->removeDecorator('Label');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);
        
        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Do action');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }


}

