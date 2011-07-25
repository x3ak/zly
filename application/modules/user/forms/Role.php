<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Role.php 1190 2011-02-10 17:04:55Z deeper $
 */
namespace User\Form;

use \Zend\Form\Element as Element;

class Role extends \Zend\Form\Form
{

    /**
     * Form inititalization
     */
    public function init()
    {

        $this->setMethod('POST');

        $element = new Element\Text('name');
        $element->setLabel('Role name:');
        $element->setRequired(true);
        $this->addElement($element);

        $rolesModel = new \User\Model\Roles();
        $roles = $rolesModel->getlist();
        $parents = array();
        foreach ($roles as $role) {
            $parents[$role->getId()] = $role->getName();
        }

        $apiRequest = new \Zly\Api\Request($this, 'sysmap.get-map-form-element');
        $actionNavigator = $apiRequest->proceed()->getResponse()->getFirst();

        if ($actionNavigator instanceof \Zly\Form\Element\Tree) {
            $actionNavigator->setName('resources');
            $actionNavigator->setMultiple(true);
            $actionNavigator->setRequired(false);
            $actionNavigator->setLabel('ACL allowed resources:');
            $this->addElement($actionNavigator);
        }

        $element = new Element\Select('parent_id');
        $element->setLabel('Parent role:');
        $element->addMultiOption('','No parents');
        $element->addMultiOptions($parents);
        $this->addElement($element);

        $submitElement = new Element\Submit('submit');
        $submitElement->setIgnore(true);
        $this->addElement($submitElement);
    }

    public function populate(array $values)
    {
        if (!empty($values['id']))
            $this->setLegend('Edit Role');

        if(!empty($values['rules'])) {
            $points = $values['rules'];
            foreach($points as $point)
                $values['resources'][] = $point->getResourceId();
        }

        return parent::populate($values);
    }

}