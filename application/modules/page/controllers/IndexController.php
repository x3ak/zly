<?php

namespace Page;

class IndexController extends \Zend\Controller\Action
{
    public function indexAction()
    {
        $this->_forward('view');
    }

    /**
     * @Qualifier \Page\Form\ListParams
     */
    public function viewAction()
    {
        $pageName = $this->getRequest()->getParam('pagename');

        if(empty($pageName) === false) {
            $modelPages = new Model\Pages();
            $page = $modelPages->getPageBySysname($pageName);

            if($page === false)
                $this->_forward('error404','error','default');

            $this->view->page = $page;
        }
        else
            $this->broker('redirector')->gotoUrl('/');
    }
}