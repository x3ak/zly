<?php
namespace Page\Form;

use \Zend\Form\Element as Element;

class ListParams extends \Zend\Form\SubForm
{
    public function init()
    {
        $page = new Element\Select('pagename');
        $page->setLabel('title');
        $page->setAllowEmpty(false)
             ->setRequired(true);

        $pagesList = Page_Model_DbTable_Page::getInstance()->getList();

        $page->addMultiOption('', '');

        foreach ($pagesList as $pageMapper)
            $page->addMultiOption($pageMapper->sysname, $pageMapper->title);

        $this->addElement($page);
    }
}