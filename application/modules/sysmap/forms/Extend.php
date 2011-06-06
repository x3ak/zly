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

        $mapTree->addDisableCondition('level', new \Zend\Validate\LessThan(3))
                ->addDisableCondition('level', new \Zend\Validate\GreaterThan(3));
        $this->addElement($mapTree);

        $submit = new \Element\Submit('submit_extension');
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

    public function isValid($data) {
        if (empty($data['sysmap_id']) === false)
            $this->_appendParamsSubform($data['sysmap_id']);

        return parent::isValid($data);
    }

    protected function _appendParamsSubform($sysmap_id)
    {
        $sysmapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $sysmap_id, Doctrine_Core::HYDRATE_ARRAY);
        $formClass = $sysmapItem['form_name'];

        if (empty($formClass) === false) {
            if (class_exists($formClass) === false)
                throw new Zend_Exception('Associated form class does not exists!');

            $paramsForm = new $formClass();

            if (($paramsForm instanceof Zend_Form_SubForm) === false)
                throw new Zend_Exception('Associated form class must be instance of Zend_Form_SubForm!');

            $this->addSubForm($paramsForm, 'params', $this->getElement('submit_extension')->getOrder() - 1);
        }
    }
}