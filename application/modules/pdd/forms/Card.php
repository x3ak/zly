<?php

namespace Pdd\Form;

use \Zend\Form\Element as Element;

class Card extends \Zend\Form\Form
{
    /**
     * Cards model
     * @var \Pdd\Model\Cards 
     */
    protected $_model;
    
    /**
     * Uploads directory path
     * @var type 
     */
    protected $_uploads = '';
    
    public function init()
    {
        $title = new Element\Text('title');
        $title->setLabel('Title')
              ->setRequired(true);
        $this->addElement($title);

        $picture = new Element\File('picture');
        $picture->getDecorator('Description')->setEscape(false);
        $picture->setLabel('Picture');
        $this->addElement($picture);
        
        $categories = $this->_model->getCategories();
        $categoryOptions = array();
        foreach ($categories as $value)
            $categoryOptions[$value->getId()] = $value->getTitle();
        $category = new Element\Select('category_id');
        $category->addMultiOptions($categoryOptions)->setLabel('Category');
        $this->addElement($category);
        
        $answer = new Element\Text('answer');
        $answer->setLabel('Answer')
              ->setRequired(true);
        $this->addElement($answer);

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
    
    public function setUploads($uploads)
    {
        $this->_uploads = $uploads;
    }
    
    public function populate($values)
    {
        if(!empty($values['picture']))
            $this->getElement('picture')->setDescription(
                    '<img src="'.str_replace(realpath(APPLICATION_PATH.'/../public'), 
                    '',$this->_uploads.'/'.$values['picture']).'"/>'
            );
        return parent::populate($values);
    }
}