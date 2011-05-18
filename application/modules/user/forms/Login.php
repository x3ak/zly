<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Login.php 821 2010-12-17 16:24:51Z deeper $
 */
class User_Form_Login extends Zend_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $loginElement = new Zend_Form_Element_Text('login');
        $loginElement->setLabel('Login:');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);

        $passwordElement = new Zend_Form_Element_Password('password');
        $passwordElement->setLabel('Password:');
        $passwordElement->setRequired(true);
        $this->addElement($passwordElement);

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Login');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}