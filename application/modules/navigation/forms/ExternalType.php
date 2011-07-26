<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 06.01.11
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */

namespace Navigation\Form;

use \Zend\form\Element as Element;

class ExternalType extends \Zend\Form\SubForm
{
    public function init()
    {
        $url = new Element\Text('external_link');
        $url->setLabel('url')
            ->setRequired(true)
            ->setAllowEmpty(false);

        $this->addElement($url);
    }
}