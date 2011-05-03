<?php
/**
 * Description
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */

/**
 * Contains methods for displaying parts of the navigation
 */
class Navigation_IndexController extends Zend_Controller_Action
{
    /**
     * Display user defined navigation
     * @paramsform Navigation_Form_DisplayMenuParams
     * @return void
     */
    public function displayMenuAction()
    {
        $itemId = $this->getRequest()->getParam('item_id');
        $this->view->nav = Navigation_Model_Navigation::getInstance()->getNavigation($itemId);

        $this->view->css = $this->getRequest()->getParam('css');
        $this->view->partial = $this->getRequest()->getParam('partial');
    }
}