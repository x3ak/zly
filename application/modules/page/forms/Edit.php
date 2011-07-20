<?php
namespace Page\Form;

use \Zend\Form\Element as Element;

class Edit extends \Zend\Form\Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $sysname = new Element\Text('sysname');
        $sysname->setAllowEmpty(false);
        $sysname->setRequired(true);
        $sysname->setLabel( 'sysname' );
        $this->addElement($sysname);

        $title = new Element\Text('title');
        $title->setAllowEmpty(false);
        $title->setRequired(true);
        $title->setLabel( 'title' );
        $this->addElement($title);

        $keywords = new Element\Textarea('content');
        $keywords->setAllowEmpty(false);
        $keywords->setRequired(true);
        $keywords->setAttrib('rows',10);
        $keywords->setLabel( 'content' );
        $this->addElement($keywords);

        $keywords = new Element\Textarea('meta_keywords');
        $keywords->setAllowEmpty(false);
        $keywords->setRequired(true);
        $keywords->setAttrib('rows',2);
        $keywords->setLabel( 'meta_keywords' );
        $this->addElement($keywords);

        $description = new Element\Textarea('meta_description');
        $description->setAllowEmpty(false);
        $description->setRequired(true);
        $description->setAttrib('rows',3);
        $description->setLabel( 'meta_description' );
        $this->addElement($description);

        $submitElement = new Element\Submit('submit');
        $submitElement->setLabel('save');

        $this->addElement($submitElement);
    }
}