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
     * @Qualifier Templater_Form_FlashMessage
     */
    public function displayFlashMessagesAction()
    {
        $messages = $this->broker('FlashMessenger')->getMessages();
        $this->view->messages = $messages;
    }
}