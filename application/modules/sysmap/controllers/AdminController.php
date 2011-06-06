<?php
/**
 * Slys 2
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * 
 */

namespace Sysmap;

/**
 * Sysmap admin controller
 */

class AdminController extends \Zend\Controller\Action
{ 
    /**
     * @var Sysmap_Model_Map 
     */
    protected $_mapModel;

    /**
     * Per page for list
     * @var int
     */
    protected $_perPage = 20;

    public function init()
    {
        
        $this->_mapModel = new \Sysmap\Model\Map();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * List the map
     *
     * Shows the list of the map
     * in hierarchy
     *
     * @return void
     */
    public function listAction()
    {
        $this->view->sysmapTree = $this->_mapModel->getSysmap();
    }


    /**
     * @return void
     */
    public function editExtendAction()
    {
        $form = new Sysmap_Form_Extend();
        $form->getElement('sysmap_id')->setValueKey('id');

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_mapModel->addExtend($form->getValues());
                return $this->_helper->redirector->gotoUrl( $this->view->url( array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list-extends'), null, true ) );
            }
        }
        else {
            $extendId = $this->getRequest()->getParam('id');
            if (empty($extendId) === false) {
                $mapItem = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $extendId);

                if (empty($mapItem) === false) {
                    $values = $mapItem->toArray();
                    $values['sysmap_id'] = $mapItem->getNode()->getParent()->id;
                    $form->populate($values);
                }
            }
        }

        $this->view->editExtensionForm = $form;
    }

    public function deleteExtendAction()
    {
        $id = $this->getRequest()->getParam('id');

        if (empty($id) === false) {
            $object = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $id);
            if (empty($object) === false) {
                $object->getNode()->delete();
            }
        }

        return $this->_helper->redirector->gotoUrl( $this->view->url( array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list-extends'), null, true ) );
    }
}