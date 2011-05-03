<?php
/**
 *  SlyS
 *
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: AdminController.php 1112 2011-01-26 10:01:50Z zak $
 */


/**
 * Administrative part for EAV Entity submodule
 */
class Eaventity_AdminController extends Zend_Controller_Action
{
    /**
     * Dashboard page
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * List of entities
     *
     * Shows list of entities
     *
     * @paramsform Eaventity_Form_EditParams
     * @return void
     */
    public function listAction()
    {

        $typeMapper = Eavtype_Model_Type::getInstance()->findById( $this->getRequest()->getParam('type') );
        if(empty($typeMapper) === false ) {
            $list = Eaventity_Model_Entity::getInstance()->findByType($typeMapper->id);
            $this->view->typeFiltered = true;
            $this->view->typeMapper = $typeMapper;
        } else {
            $list = Eaventity_Model_Entity::getInstance()->getList(Doctrine_Core::HYDRATE_RECORD);
        }

        $this->view->list = $list;
    }

    /**
     * New/Edit entity page
     *
     * @paramsform Eaventity_Form_EditParams
     * @return void
     */
    public function editAction()
    {
        $form = new Eaventity_Form_Edit();

        $typeMapper = Eavtype_Model_Type::getInstance()->findById( $this->getRequest()->getParam('type') );
        $mapper = new Eaventity_Model_Mapper_Entity();

        if($this->getRequest()->getParam('id') !== null) {
            $mapper->assignIdentifier($this->getRequest()->getParam('id'));
            $mapper->EntityValues;
        }

        if($mapper->exists() === false) {
            $mapper->type_id = $typeMapper->id;
        }

        if($typeMapper === false && $mapper->exists() === false) {
            $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_not_found');
            $this->_helper->redirector->gotoUrlAndExit(
                $this->view->url(array('module'=>'eaventity','controller'=>'admin','action'=>'list'),'admin',true)
            );
        }



        if($mapper->exists()) {
            $form->prepareForType($mapper->type_id, $mapper);
        } else {
            $form->prepareForType($typeMapper->id);
        }



        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $mapper->fromArray($form->getValues());
                $mapper->save();


                $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_saved');
                $this->_helper->redirector->gotoUrlAndExit(
                    $this->view->url(array('module'=>'eaventity','controller'=>'admin','action'=>'edit','id' => $mapper->id),'admin',true)
                );
            }
        }

        $form->populate($mapper->toArray());
        $this->view->mapper = $mapper;
        $this->view->form = $form;
    }

}