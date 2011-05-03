<?php

/**
 * Slys
 *
 * Navigation module bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1139 2011-01-28 16:07:30Z criolit $
 */
class Navigation_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initRegisterHelper()
    {
        if (!$this->getApplication()->hasPluginResource('view'))
            $this->getApplication()->registerPluginResource('view');

        $this->getApplication()->bootstrap('view');

        $this->getApplication()->getResource('view')->registerHelper(
                new Navigation_View_Helper_AdminCurrentSubmenu(),
                'adminCurrentSubmenu'
        );

        $this->getApplication()->getResource('view')->registerHelper(
                new Navigation_View_Helper_ArrayTreeToTable(),
                'arrayTreeToTable'
        );
    }

    protected function _initPlugins()
    {
        Zend_Controller_Front::getInstance()->registerPlugin(new Navigation_Plugin_Init());
    }
}