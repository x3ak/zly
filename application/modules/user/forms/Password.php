<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Profile.php 1012 2011-01-12 14:50:23Z deeper $
 */
namespace User\Form;
use \Zend\Form\Element as Element;

class Password extends \Zend\Form\Form
{

    public function init()
    {
        $element = new Element\Text('password');
        $element->setLabel('Enter old password:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('new_password');
        $element->setLabel('Enter new password:');
        $element->setRequired(true);
        $this->addElement($element);

        $submitElement = new Element\Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}