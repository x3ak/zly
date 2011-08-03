<?php

namespace Pdd\Form;

use \Zend\Form\Element as Element;

class Category extends \Zend\Form\Form
{
    /**
     * Cards model
     * @var \Pdd\Model\Cards 
     */
    protected $_model;
    
    public function init()
    {
        $title = new Element\Text('title');
        $title->setLabel('Title')
              ->setRequired(true);
        $this->addElement($title);

        $submit = new Element\Submit('submit');
        $submit->setLabel('Save')
               ->setOrder(20);
        $this->addElement($submit);
    }
    
    public function setModel(\Pdd\Model\Cards $model)
    {
        $this->_model = $model;
        return $this;
    }
}