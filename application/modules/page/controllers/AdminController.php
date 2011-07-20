<?php

namespace Page;

class AdminController extends \Zend\Controller\Action 
{
    public function indexAction()
    {
        $modelPages = new Model\Pages();
        $list = $modelPages->getList();
        $this->view->list = $list;
    }

    public function addAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $pageId = $this->getRequest()->getParam('id');
        $pagesModel = new Model\Pages();
        if(!empty($pageId)) {            
            $pageMapper = $pagesModel->getPageById($pageId);
            $this->view->title = $this->view->broker('translate')->direct('page') . ' &laquo' . $pageMapper->getTitle() .'&raquo;';
        } else {
            $pageMapper = new Model\Mapper\Page();
            $this->view->title = $this->view->broker('translate')->direct('new_page');
        }

        $form = new Form\Edit();
        $form->populate($pageMapper->toArray());

        if ( $this->getRequest()->isPost() ) {
            if ( $form->isValid( $this->getRequest()->getPost() ) ) {
                $pagesModel->savePage($pageMapper, $form->getValues());

                $this->broker('FlashMessenger')->addMessage('save_ok');
                $this->broker('redirector')->goToRoute(array('module' => 'page','action'=>'index'), 'admin', true);
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $pageMapper = Page_Model_DbTable_Page::getInstance()->findOneBy('id',$id);
        $pageMapper->delete();

        $this->broker('FlashMessenger')->addMessage('save_ok');
        $this->broker('redirector')->goToRoute(array('module' => 'page', 'action'=>'index'), 'admin', true);
    }
}
