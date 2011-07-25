<?php
namespace Zlyman\Form;

class Modules extends \Zend\Form\Form
{

    public function init()
    {
        $this->setMethod('POST');
        
        
        $loginElement = new \Zend\Form\Element\Select('action');
        $loginElement->setLabel('Action:');
        $loginElement->setRequired(true)->addMultiOptions(array('install'=>'Install selected'));
        $this->addElement($loginElement);

        $loginElement = new \Zend\Form\Element\MultiCheckbox('modules');
        $loginElement->removeDecorator('Label');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);
        
        $submitElement = new \Zend\Form\Element\Submit('submit');
        $submitElement->setLabel('Do action');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }


}

