<?php

namespace User\Form\Install;

class Admin extends \Zend\Form\Form
{

    public function init()
    {
        $this->setMethod('POST');

        $loginElement = new \Zend\Form\Element\Text('admin_name');
        $loginElement->setLabel('Administrator login:')->setValue('admin');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);        

        $passwordElement = new \Zend\Form\Element\Password('admin_password');
        $passwordElement->setLabel('Administrator password:');
        $passwordElement->setRequired(true);
        $this->addElement($passwordElement);

        $loginElement = new \Zend\Form\Element\Text('admin_role');
        $loginElement->setLabel('Administrator role name:')->setValue('admin');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);
        
        $loginElement = new \Zend\Form\Element\Text('guest_role');
        $loginElement->setLabel('Guest role name:')->setValue('guest');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);

        $submitElement = new \Zend\Form\Element\Submit('save');
        $submitElement->setLabel('Save');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}