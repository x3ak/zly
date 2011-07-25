<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Login.php 821 2010-12-17 16:24:51Z deeper $
 */

namespace User\Form;

use \Zend\Form\Element as Element;

class Login extends \Zend\Form\Form
{

    public function init()
    {

        $this->setMethod('POST');

        $loginElement = new Element\Text('login');
        $loginElement->setLabel('Login:');
        $loginElement->setRequired(true);
        $this->addElement($loginElement);

        $passwordElement = new Element\Password('password');
        $passwordElement->setLabel('Password:');
        $passwordElement->setRequired(true);
        $this->addElement($passwordElement);

        $submitElement = new Element\Submit('submit');
        $submitElement->setLabel('Login');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}