<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Profile.php 1016 2011-01-13 13:16:42Z deeper $
 */
namespace User\Form;

use \Zend\Form\Element as Element;

class Profile extends \Zend\Form\Form
{

    public function init()
    {
        $action = $this->getView()->broker('url')->direct(
                array('action'=>'index','controller'=>'profile','module'=>'user'),
                null,
                true);
        $this->setMethod('POST');
        $this->setAction($action);

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

        $submitElement = new Element\Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

}