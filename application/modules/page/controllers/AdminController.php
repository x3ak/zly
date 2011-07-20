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
        if(!empty($pageId)) {
            $pagesModel = new Model\Pages();
            $pageMapper = $pagesModel->getPageById($pageId);
            $this->view->title = $this->view->translate('page') . ' &laquo' . $pageMapper->title .'&raquo;';
        } else {
            $pageMapper = new Model\Mapper\Page();
            $this->view->title = $this->view->translate('new_page');
        }

        $form = new Form\Edit();
        $form->populate($pageMapper->toArray());

        if ( $this->getRequest()->isPost() ) {
            if ( $form->isValid( $this->getRequest()->getPost() ) ) {

                $pageMapper->fromArray($form->getValues());
                $pageMapper->save();

                $this->broker('FlashMessenger')->addMessage('save_ok');
                $this->broker('redirector')->goToRoute(array('module' => 'page'), 'admin', true);
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
        $this->broker('redirector')->goToRoute(array('module' => 'page'), 'admin', true);
    }
}
