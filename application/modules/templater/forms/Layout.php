<?php

/**
 * SlyS
 *
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * @version    $Id: Layout.php 1134 2011-01-28 14:31:15Z deeper $
 */
namespace Templater\Form;

use \Zend\Form\Element as Element;

class Layout extends \Zend\Form\Form
{
    /**
     * Form initialization
     */
    public function init()
    {
        $this->loadDefaultDecorators();
        $this->setLegend('New layout');
        $this->addDecorator('fieldset');
        $this->setMethod('POST');
        $element = new Element\Text('title');
        $element->setLabel('Title:')
                ->setRequired(true);
        $this->addElement($element);

        $themeModel = new \Templater\Model\Themes();
        $themes = $themeModel->getThemesPaginator(1, 10000);

        $themesList = array();
        foreach ($themes as $theme) {
            $themesList[$theme->getId()] = $theme->getTitle();
        }

        $element = new Element\Select('theme_id');
        $element->setLabel('Theme:')
                ->addMultiOptions($themesList)
                ->setRequired(true);
        $this->addElement($element);

        $element = new Element\Text('name');
        $element->setLabel('Layout file:')
                ->setRequired(true);
        $this->addElement($element);

        $element = new Element\Checkbox('published');
        $element->setLabel('Published:');
        $this->addElement($element);

        $apiRequest = new \Slys\Api\Request($this, 'sysmap.get-map-form-element');
        $navigator = $apiRequest->proceed()->getResponse()->getFirst();

        if($navigator instanceof \Slys\Form\Element\Tree) {
            $navigator->setName('map_id');
            $navigator->setMultiple(true);
            $this->addElement($navigator);
        }

        $element = new Element\Hidden('module');
        $element->removeDecorator('Label')
                ->removeDecorator('HtmlTag')
                ->setValue($this->_defaultValue);
        $this->addElement($element);

        $element = new Element\Hidden('controller');
        $element->removeDecorator('Label')
                ->setValue($this->_defaultValue)
                ->removeDecorator('HtmlTag');
        $this->addElement($element);

        $element = new Element\Hidden('action');
        $element->removeDecorator('Label')
                ->setValue($this->_defaultValue)
                ->removeDecorator('HtmlTag');
        $this->addElement($element);

        $element = new Element\Submit('submit');
        $element->setLabel('Save')
                ->setIgnore(true);
        $this->addElement($element);
    }

    public function  populate(array $values)
    {
        if(!empty($values['id']))
            $this->setLegend('Edit layout');

        if(!empty($values['Points'])) {
            $points = $values['Points'];
            unset($values['Points']);
            foreach($points as $point)
                $values['map_id'][] = $point['map_id'];
        }

        return parent::populate($values);
    }

}