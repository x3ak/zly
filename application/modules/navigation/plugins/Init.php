<?php

/**
 * Slys
 *
 * Template layout switcher. Used to check and switch layout for the current theme
 * such file exists
 *
 * @author Serghei Ilin <criolit@gmail.com>
 */
namespace Navigation\Plugin;

class Init extends \Zend\Controller\Plugin\AbstractPlugin
{
    /**
     * On dispatch loop startup initializing global navigation
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        \Zend\Registry::set('Zend\Navigation', Navigation\Model\Navigation::getInstance()->getNavigation());
    }
}