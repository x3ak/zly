<?php

namespace Pdd;

class AdminController extends \Zend\Controller\Action 
{
    public function indexAction()
    {
        
    }
    
    public function cardsAction()
    {
        $page = $this->getRequest()->getParam('page');
        $model = new Model\Cards();
        $cards = $model->getCards($page);
        $this->view->cards = $cards;
    }
    
    public function editCardAction()
    {
        $model = new Model\Cards();
        $id = $this->getRequest()->getParam('id');
        if($id)
            $card = $model->getCardById($id);
        else
            $card = new Model\Mapper\Card;
        $options = $this->getInvokeArg('bootstrap')->getOption('pdd');
        $uploads = realpath($options['upload_directory']);
        $form = new Form\Card(array('model'=>$model, 'uploads'=>$uploads));
        
        if($this->getRequest()->isPost()) {
            
            if($form->isValid($this->getRequest()->getPost())) {
                $result = $model->saveCard($card, $form->getValues());
                if($result) { 
                    
                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->setDestination($uploads);
                    if (!$adapter->receive()) {
                        $messages = $adapter->getMessages();
                    } else {
                        $form->setErrors(array('Picure not uploaded'));
                        continue;
                    }
            
                    $this->broker('flashmessenger')->addMessage('Card saved');
                    $this->broker('redirector')->goToRoute(array('module' => 'pdd', 'action' => 'cards'), 'admin', true);
                    return ;
                }
            }
        
        }

        $form->populate($card->toArray());
        $this->view->form = $form;
    }
    
    public function deleteCardAction()
    {
        $model = new Model\Cards();
        $id = $this->getRequest()->getParam('id');
        if($id)
            $card = $model->getCardById($id);
        
        if($card)
            $result = $model->deleteCard($card);
        
        if($result) {
            $this->broker('flashmessenger')->addMessage('Card removed');                
        }
        
        return $this->broker('redirector')->goToRoute(array('module' => 'pdd', 'action' => 'cards'), 'admin', true);
    }
    
    public function categoriesAction()
    {
        $page = $this->getRequest()->getParam('page');
        $model = new Model\Cards();
        $categories = $model->getCategories($page);
        $this->view->categories = $categories;
    }
    
    public function editCategoryAction()
    {
        $model = new Model\Cards();
        $id = $this->getRequest()->getParam('id');
        if($id)
            $category = $model->getCategoryById($id);
        else
            $category = new Model\Mapper\Category;

        $form = new Form\Category();
        
        if($this->getRequest()->isPost()) {
            
            if($form->isValid($this->getRequest()->getPost())) {
                $result = $model->saveCategory($category, $form->getValues());
                if($result) { 
            
                    $this->broker('flashmessenger')->addMessage('Category saved');
                    $this->broker('redirector')->goToRoute(array('module' => 'pdd', 'action' => 'categories'), 'admin', true);
                    return ;
                }
            }
        
        }

        $form->populate($category->toArray());
        $this->view->form = $form;
    }
    
    public function deleteCategoryAction()
    {
        $model = new Model\Cards();
        $id = $this->getRequest()->getParam('id');
        if($id)
            $category = $model->getCategoryById($id);
        
        if($category)
            $result = $model->deleteCategory($category);
        
        if($result) {
            $this->broker('flashmessenger')->addMessage('Category removed');                
        }
        
        return $this->broker('redirector')->goToRoute(array('module' => 'pdd', 'action' => 'categories'), 'admin', true);
    }
}