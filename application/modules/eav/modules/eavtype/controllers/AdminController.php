<?php
/**
 *  SlyS
 *
 * @author     Pavel Galaton <pavel.galaton@gmail.com>
 * @version    $Id: AdminController.php 1147 2011-01-31 13:13:06Z zak $
 */


/**
 * Administrative part for EAV Type submodule
 */
class Eavtype_AdminController extends Zend_Controller_Action
{
    /**
     * Dashboard page
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * List of types
     *
     * Shows list of types
     *
     * @return void
     */
    public function listAction()
    {
        $this->view->list = Eavtype_Model_Type::getInstance()->getList(Doctrine_Core::HYDRATE_ARRAY);
    }

    /**
     * New type creation page
     *
     * @return void
     */
    public function newAction()
    {
        $form = new Eavtype_Form_New();

        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $mapper = Eavtype_Model_Type::getInstance()->createNew($this->getRequest()->getPost());

                $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_saved');
                $this->_helper->redirector->gotoUrlAndExit(
                    $this->view->url(array('module'=>'eavtype','controller'=>'admin','action'=>'edit','id' => $mapper->id),'admin',true)
                );
            }
        }

        $this->view->form = $form;
    }

    /**
     * Page that displays controlls for attributes movement
     *
     * Action requires id parameter
     *
     * @return void
     */
    public function moveAttributesAction()
    {
        $form = new Eavtype_Form_MoveAttributes();
        $mapper = Eavtype_Model_Type::getInstance()->findById( $this->getRequest()->getParam('id') );
        if( empty($mapper) ) {
            $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_not_found');
            $this->_helper->redirector->gotoUrlAndExit(
                $this->view->url(array('module'=>'eavtype','controller'=>'admin','action'=>'list'),'admin',true)
            );
        }


        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
                $data = $form->getValues();
                Eavtype_Model_AttrRelation::getInstance()->moveAttributes($data['source_attr_id'], $data['target_attr_id'], $mapper->id);

                $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_saved');
                $this->_helper->redirector->gotoUrlAndExit(
                    $this->view->url(array('module'=>'eavtype','controller'=>'admin','action'=>'move-attributes','id' => $mapper->id),'admin',true)
                );
            }
        } else {
            $form->populate($mapper->toArray());
        }

        $this->view->mapper = $mapper;
        $this->view->form = $form;
    }

    /**
     * Edit type page
     *
     * Action requires id parameter
     *
     * @return void
     */
    public function editAction()
    {
        $form = new Eavtype_Form_Edit();

        $mapper = Eavtype_Model_Type::getInstance()->findById( $this->getRequest()->getParam('id') );

        if(empty($mapper)) {
            $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_not_found');
            $this->_helper->redirector->gotoUrlAndExit(
                $this->view->url(array('module'=>'eavtype','controller'=>'admin','action'=>'list'),'admin',true)
            );
        }



        if($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost();

            if(!empty($postData['available_attr_id'])) {
                $entityAttribute = Eavattribute_Model_Attribute::getInstance()->findBySysname('entity');
                if(in_array($entityAttribute->id, $postData['available_attr_id'])) {
                    if(empty($postData['entity_type_id'])) {
                        $form->populate( $mapper->toArray() );
                        $form->addSelectEntityType();
                    }
                }
            }

            if($form->isValid($postData)) {
                $mapper = Eavtype_Model_Type::getInstance()->doEdit($mapper, $postData);


                $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_saved');
                $this->_helper->redirector->gotoUrlAndExit($this->view->url(array('module'=>'eavtype','controller'=>'admin','action'=>'edit','id'=>$mapper->id),'admin',true));
            }
        } else {
            $form->populate( $mapper->toArray() );
        }


        $this->view->mapper = $mapper;
        $this->view->form = $form;
    }

    public function deleteAttributesAction()
    {
        $form = new Eavtype_Form_DeleteAttributes();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $postData = $form->getValues();

                Eavtype_Model_Type::getInstance()->deleteAttributes($postData['id'], $postData['used_attr_id'], (boolean)$postData['delete_from_childs']);

                $this->_helper->getHelper('FlashMessenger')->addMessage('item_was_saved');
                $this->_helper->redirector->gotoUrlAndExit(
                    $this->view->url(array('module'=>'eavtype','controller'=>'admin','action'=>'delete-attributes','id' => $postData['id']),'admin',true)
                );
            }
        }
        elseif($this->getRequest()->getParam('id') !== null) {
            $type = Eavtype_Model_Type::getInstance()->findById( $this->getRequest()->getParam('id') );

            if (empty($type) === false) {
                $form->populate( $type->toArray() );

                $ancestors = array();
                $ancestorsObj = $type->getNode()->getAncestors();

                if (empty($ancestorsObj) === false)
                    $ancestors = $ancestorsObj->toArray();

                $ancestors[] = array('id' => $type->id, 'name' => $type->name);

                $this->view->typeBreadcrumb = $ancestors;
                $this->view->currentType = $type;
            }
        }

        $this->view->form = $form;
    }
}