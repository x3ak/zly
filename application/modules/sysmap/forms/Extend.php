<?php

namespace Sysmap\Form;

use \Zend\Form\Element as Element;

class Extend extends \Zend\Form\Form
{
    /**
     * Sysmap model
     * @var \Sysmap\Model\Map  
     */
    protected $_model;
    
    public function __construct($model = null, $options = null) 
    {
        $this->_model = $model;
        parent::__construct($options);
    }
    public function init()
    {
        $title = new Element\Text('name');
        $title->setLabel('Name')
              ->setRequired(true)
              ->setAttrib('maxLength', 100);

        $this->addElement($title);

//        $apiRequest = new \Zly\Api\Request($this, 'sysmap.get-map-tree');
//        $mapTree = $apiRequest->proceed()->getResponse()->getFirst();
//        $mapTree->setLabel('Extension parent node');
//        $mapTree->addDisableCondition('level', new \Zend\Validator\LessThan(3))
//                ->addDisableCondition('level', new \Zend\Validator\GreaterThan(3));
//        $this->addElement($mapTree);

        $submit = new Element\Submit('submit_extension');
        $submit->setLabel('Save')
               ->setOrder(20);
        $this->addElement($submit);

        $this->addElement('hidden', 'hash');
        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'sysmap_id');
    }

    /**
     * Set up values from array
     * @param array $values
     * @return Zend_Form
     */
    public function populate(array $values)
    {
        if (!empty($values['sysmap_id']))
            $this->_appendParamsSubform($values['sysmap_id']);
        elseif (!empty($values['hash'])) {
            $mapitem = $this->_model->getParentByHash($values['hash']);
            $this->_appendParamsSubform($mapitem->hash);
        }

        return parent::populate($values);
    }

    public function isValid($data) 
    {

        if (!empty($data['sysmap_id']))
            $this->_appendParamsSubform($data['sysmap_id']);
        
        return parent::isValid($data);
    }

    protected function _appendParamsSubform($sysmap_id)
    {
        $sysmapItem = $this->_model->getNodeByHash($sysmap_id);
        if(empty($sysmapItem->Qualifier))
                return false;
        $formClass = $sysmapItem->Qualifier;

        if (!empty($formClass)) {
            if (!class_exists($formClass))
                throw new \Zend\Form\Exception\UnexpectedValueException('Associated form class does not exists!');

            $paramsForm = new $formClass();

            if (!$paramsForm instanceof \Zend\Form\SubForm)
                throw new \Zend\Form\Exception\UnexpectedValueException('Associated form class must be instance of Zend_Form_SubForm!');

            $this->addSubForm($paramsForm, 'params', $this->getElement('submit_extension')->getOrder() - 1);
        }
    }
}