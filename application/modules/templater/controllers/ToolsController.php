<?php

/**
 *	SlyS
 *
 * @abstract   contains Templater_ToolsController class, extending Zend_Controller_Action
 * @author     Evgheni Poleacov <evgheni.poleacov@gmail.com>
 *
 * @version    $Id: ToolsController.php 763 2010-12-14 12:21:26Z deeper $
 */

namespace Templater;

class ToolsController extends \Zend\Controller\Action
{
    /**
     * Display flash system messages
     *
     * @widget Display flash messages
     * @form Templater_Form_FlashMessage
     */
    public function displayFlashMessagesAction()
    {
        $messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        $this->view->messages = $messages;
    }
}