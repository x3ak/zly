<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: User.php 806 2010-12-16 17:07:02Z deeper $
 */
namespace User\Form;

use \Zend\Form\Element as Element;

class User extends \Zend\Form\Form
{

    public function init()
    {
        $this->setMethod('POST');

        $element = new Element\Text('login');
        $element->setLabel('User login:');
        $element->setOrder(-100);
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('firstname');
        $element->setLabel('Firstname:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('lastname');
        $element->setLabel('Lastname:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('patronymic');
        $element->setLabel('Patronymic:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('email');
        $element->setLabel('Email:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('phone');
        $element->setLabel('Phone:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('region');
        $element->setLabel('Region:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('city');
        $element->setLabel('City:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('zip');
        $element->setLabel('ZIP:');
        $this->addElement($element);

        $element = new Element\Text('address');
        $element->setLabel('Address:');
        $this->addElement($element);

        $element = new Element\Text('mobile_code');
        $element->setLabel('Mobile Phone Code:');
        $this->addElement($element);

        $element = new Element\Text('mobile_number');
        $element->setLabel('Mobile Phone Number:');
        $this->addElement($element);

        $element = new Element\Select('role_id');
        $element->setLabel('User role:');
        $element->setRequired(true);
        $this->addElement($element);

        $submitElement = new Element\Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }
    
    public function populate(array $values) 
    {
        if(empty($values['id'])) {
            $element = new Element\Password('password');
            $element->setLabel('Password:');
            $element->setOrder(-3);
            $element->setRequired(true);
            $this->addElement($element);
            
            $element = new Element\Checkbox('active');
            $element->setLabel('Active:');
            $element->setOrder(-2);
            $element->setRequired(true);
            $this->addElement($element);
        }
            
        return parent::populate($values);
    }

}