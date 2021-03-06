<?php

/**
 * Zly 2
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * 
 */

namespace Sysmap;

/**
 * Sysmap admin controller
 */
class AdminController extends \Zend\Controller\Action {

    /**
     * @var Sysmap_Model_Map 
     */
    protected $_mapModel;
    /**
     * Per page for list
     * @var int
     */
    protected $_perPage = 20;

    public function init() {

        $this->_mapModel = new \Sysmap\Model\Map();
    }

    public function indexAction() {
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
    public function listAction() {

        $this->view->sysmapTree = $this->_mapModel->getSysmap();
    }

    /**
     * @return void
     */
    public function editExtendAction() {
        $form = new Form\Extend($this->_mapModel);
        $params = $this->getRequest()->getParams();

        $form->populate($params);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_mapModel->saveExtension($form->getValues());
                return $this->broker('redirector')->gotoUrl(
                        $this->view->broker('url')->direct(
                                array(
                            'module' => 'sysmap',
                            'controller' => 'admin',
                            'action' => 'list'), null, true));
            }
        } else {
            $hash = $this->getRequest()->getParam('hash');
            if (!empty($hash)) {                
                
                $values = $this->_mapModel->getNodeByHash($hash);
                if($values instanceof Model\Mapper\Extend)
                    $values = $values->toArray();

                if($values['params'] instanceof \Zend\Config\Config)
                    $values['params'] = $values['params']->toArray();

                if (!empty($values)) {
                    $values['sysmap_id'] = $this->_mapModel->getParentByHash($hash)->hash;
                    $form->populate($values);
                }
            }
        }

        $this->view->editExtensionForm = $form;
    }

    public function deleteExtendAction() {
        $id = $this->getRequest()->getParam('id');

        if (empty($id) === false) {
            $object = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('id', $id);
            if (empty($object) === false) {
                $object->getNode()->delete();
            }
        }

        $this->_helper->redirector->gotoUrl($this->view->url(
                array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true));
    }
    
    /**
     * Clean sysmap extensions cache
     */
    public function cleanExtensionsCacheAction()
    {
        $this->_mapModel->clearExtensionsCache();
        $this->broker('redirector')->gotoUrl($this->view->broker('url')->direct(
                array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true));
    }
    
    /**
     * Clean complete sysmap cache
     */
    public function cleanCacheAction()
    {
        $this->_mapModel->clearExtensionsCache();
        $this->broker('redirector')->gotoUrl($this->view->broker('url')->direct(
                array('module' => 'sysmap', 'controller' => 'admin', 'action' => 'list'), null, true));
    }

}