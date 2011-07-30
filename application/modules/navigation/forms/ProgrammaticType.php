<?php
/**
 * User: criollit
 */

namespace Navigation\Form;

use \Zend\Form\Element;

class ProgrammaticType extends \Zend\Form\SubForm
{
    public function init()
    {
        /**
         * @var $map \Zly\Form\Element\Tree
         */
        $mapTree = \Zly\Api\ApiService::getInstance()->request(new \Zly\Api\Request($this, 'sysmap.get-map-form-element'))->getFirst();
        $mapTree->setName('sysmap_identifier')
                ->addDisableCondition('level', new \Zend\Validator\LessThan(1));
        $this->addElement($mapTree);
    }
}