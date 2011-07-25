<?php

/**
 * Zly
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: Theme.php 867 2010-12-22 12:44:26Z deeper $
 */
namespace Templater\Form;

use \Zend\Form\Element as Element;

class Theme extends \Zend\Form\Form
{

    public function init()
    {
        $this->setMethod('POST');
        $element = new Element\Text('title');
        $element->setLabel('Title:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Select('name');
        $element->setLabel('Directory:');
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Radio('current');
        $element->setSeparator('&nbsp;');
        $element->setLabel('Current:');
        $element->setValue(false);
        $element->setMultiOptions(array('1' => 'Yes', '0' => 'No'));
        $element->setRequired(true);
        $this->addElement($element);

        $element = new Element\Radio('import_layouts');
        $element->setSeparator('&nbsp;');
        $element->setLabel('Import layouts:');
        $element->setValue(false);
        $element->setMultiOptions(array('1' => 'Yes', '0' => 'No'));
        $this->addElement($element);

        $element = new Element\Submit('submit');
        $element->setLabel('Save');
        $element->setIgnore(true);
        $this->addElement($element);
    }

}