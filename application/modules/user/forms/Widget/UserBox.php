<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: UserBox.php 838 2010-12-21 10:54:03Z deeper $
 */
namespace User\Form\Widget;
class UserBox extends \Zend\Form\SubForm
{

    public function init()
    {
        $element = new \Zend\Form\Element\Select('box_type');
        $element->setLabel('Box type:');
        $element->addMultiOption('simple', 'Simple');
        $element->addMultiOption('detailed', 'Detailed');
        $element->setRequired(true);
        $this->addElement($element);
    }

}