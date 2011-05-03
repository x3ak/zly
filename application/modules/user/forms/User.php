<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: User.php 806 2010-12-16 17:07:02Z deeper $
 */
class User_Form_User extends Zend_Form
{

    public function init()
    {
        $this->setMethod('POST');

        $element = new Zend_Dojo_Form_Element_TextBox('login');
        $element->setLabel('User login:');
        $element->setAttrib('disabled', 'disabled');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('firstname');
        $element->setLabel('Firstname:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('lastname');
        $element->setLabel('Lastname:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('patronymic');
        $element->setLabel('Patronymic:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('email');
        $element->setLabel('Email:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('phone');
        $element->setLabel('Phone:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('region');
        $element->setLabel('Region:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('city');
        $element->setLabel('City:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('zip');
        $element->setLabel('ZIP:');
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('address');
        $element->setLabel('Address:');
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('mobile_code');
        $element->setLabel('Mobile Phone Code:');
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_TextBox('mobile_number');
        $element->setLabel('Mobile Phone Number:');
        $this->addElement($element);

        $element = new Zend_Dojo_Form_Element_FilteringSelect('role_id');
        $element->setLabel('User role:');
        $element->setRequired(true);
        $this->addElement($element);

        $submitElement = new Zend_Dojo_Form_Element_SubmitButton('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}