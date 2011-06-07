<?php

namespace Sysmap\Form;

use \Zend\Form\Element as Element;

class Extend extends \Zend\Form\Form
{
    public function init()
    {
        $title = new Element\Text('title');
        $title->setLabel('title')
              ->setRequired(true)
              ->setAttrib('maxLength', 100);

        $this->addElement($title);

        $apiRequest = new \Slys\Api\Request($this, 'sysmap.get-map-tree');
        $mapTree = $apiRequest->proceed()->getResponse()->getFirst();
        
//        \Zend\Debug::dump($mapTree);

        $mapTree->addDisableCondition('level', new \Zend\Validator\LessThan(3))
                ->addDisableCondition('level', new \Zend\Validator\GreaterThan(3));
        $this->addElement($mapTree);

        $submit = new Element\Submit('submit_extension');
        $submit->setLabel('save')
               ->setOrder(20);
        $this->addElement($submit);

        $this->addElement('hidden', 'id');
    }

    /**
     * Set up values from array
     * @param array $values
     * @return Zend_Form
     */
    public function populate(array $values)
    {
        if (empty($values['sysmap_id']) === false)
            $this->_appendParamsSubform($values['sysmap_id']);

        return parent::populate($values);
    }

    public function isValid($data) 
    {
        if (empty($data['sysmap_id']) === false)
            $this->_appendParamsSubform($data['sysmap_id']);

        return parent::isValid($data);
    }

    protected function _appendParamsSubform($sysmap_id)
    {
        $model = new \Sysmap\Model\Map();
        $sysmapItem = $model->getNodeByHash($sysmap_id);
        $formClass = $sysmapItem['Qualifier'];

        if (empty($formClass) === false) {
            if (class_exists($formClass) === false)
                throw new \Zend\Form\Exception\UnexpectedValueException('Associated form class does not exists!');

            $paramsForm = new $formClass();

            if (($paramsForm instanceof \Zend\Form\SubForm) === false)
                throw new \Zend\Form\Exception\UnexpectedValueException('Associated form class must be instance of Zend_Form_SubForm!');

            $this->addSubForm($paramsForm, 'params', $this->getElement('submit_extension')->getOrder() - 1);
        }
    }
}