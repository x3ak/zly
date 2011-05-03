<?php
/**
 *  SlyS
 *
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: AdminController.php 1133 2011-01-28 14:23:33Z zak $
 */


/**
 * Administrative part for EAV Attribute submodule
 */
class Eavattribute_AdminController extends Zend_Controller_Action
{
    /**
     * Dashboard page
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * List of attributes
     *
     * Shows list of attributes with their data types
     *
     * @return void
     */
    public function listAction()
    {
        $list = Eavattribute_Model_Attribute::getInstance()->getList();
        $this->view->list = $list;
    }

    /**
     * Add/Edit attribute page
     *
     * @return void
     */
    public function editAction()
    {
        $form = new Eavattribute_Form_Edit();

        $attributeId = $this->getRequest()->getParam('id');
        $mapper = Eavattribute_Model_Attribute::getInstance()->findById($attributeId);


        if(empty($mapper)) {
            $mapper = new Eavattribute_Model_Mapper_Attribute();

        } else {
            if($mapper->status != Eavattribute_Model_DbTable_Attribute::STATUS_DEFAULT) {
                $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_not_found');
                $this->_helper->redirector->gotoUrlAndExit($this->view->url(array('module'=>'eavattribute','controller'=>'admin','action'=>'list'),'admin',true));
            }
        }

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $mapper->fromArray($form->getValues());
                $mapper->save();

                $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_saved');
                $this->_helper->redirector->gotoUrlAndExit($this->view->url(array('module'=>'eavattribute','controller'=>'admin','action'=>'edit','id'=>$mapper->id),'admin',true));
            }
        }
        else {
            $form->populate($mapper->toArray());
        }

        $this->view->mapper = $mapper;
        $this->view->form = $form;
    }
}