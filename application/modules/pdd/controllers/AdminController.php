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
        
        $form = new Form\Card(array('model'=>$model));
        
        if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            
            $result = $model->saveCard($card, $form->getValues());
            
            if($result) {
                
                $adapter = new \Zend\File\Transfer\Adapter\Http();

                $options = $this->getInvokeArg('bootstrap')->getOption('pdd');
                $adapter->setDestination($options['upload_directory']);

                if (!$adapter->receive()) {
                    $messages = $adapter->getMessages();
                }
                
                $this->broker('FlashMessenger')->addMessage('Card saved');
                $this->broker('redirector')->goToRoute(array('module' => 'pdd', 'action' => 'index'), 'admin', true);
                return ;
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
            $this->broker('FlashMessager')->addMEssage('Card saved');                
        }
        
        return $this->broker('redirector')->goToRoute(array('module' => 'pdd', 'action' => 'index'), 'admin', true);
    }
}