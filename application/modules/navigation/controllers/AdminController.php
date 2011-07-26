<?php
/**
 * Navigation administrative controller
 *
 * @version    $Id: AdminController.php 1136 2011-01-28 15:50:56Z criolit $
 */
namespace Navigation;

class AdminController extends \Zend\Controller\Action
{
	/**
	 * @var Navigation_Model_Navigation
	 */
	protected $_navigationModel = null;

	public function init()
	{
            $this->_navigationModel = new Model\Navigation();
	}

        /**
         *
         * @return void
         */
	public function indexAction()
	{
            
	}

	/**
	 * List structured menu
	 */
	public function listMenuAction()
	{
            $this->view->tree = array();
            $tree = $this->_navigationModel
                         ->getStructureTree(array('id', 'title', 'route', 'read_only'));

            if(!empty($tree))
                $this->view->tree = array( $tree );
	}

	/**
	 * Editing menu item (leaf node)
	 */
	public function editMenuItemAction()
	{
		$id = $this->getRequest()->getParam('id', null);
		if ($id !== null) {
                $item = $this->_navigationModel->getItem($id);
			if (empty($item) === false and $item->read_only === true) {
				$this->broker('FlashMessenger')->addMessage('That was a READ ONLY navigation item');
				return $this->broker('redirector')->goToRoute(array('module' => 'navigation', 'action' => 'list-menu'), 'admin', true);

			}
		}

		$form = new Form\MenuItem(array('model'=>$this->_navigationModel));
                
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->getRequest()->getPost())) {
				$this->_navigationModel->saveLeafItem( $form->getValues() );
                                $this->broker('FlashMessenger')->addMessage('Menu item successful saved.');
                                return $this->broker('redirector')->goToRoute(array('module' => 'navigation', 'action' => 'list-menu'), 'admin', true);
		
			}
		}
		else {
			if ($id !== null) {
				$itemData = $this->_navigationModel->getItem($id);
                if (empty($itemData) === false) {
                    $correctParentId = $itemData->getNode()->getParent()->id;

                    $itemData = $itemData->toArray();
                    $itemData['parent_id'] = $correctParentId;

                    $form->populate($itemData);
                }
                else {
                    $this->broker('FlashMessenger')->addMessage('Menu item successful saved.');
                    return $this->broker('redirector')->goToRoute(array('module' => 'navigation', 'action' => 'edit-menu-item'), 'admin', true);

                }
			}
		}

		$this->view->menuItemForm = $form;
	}

	/**
	 * Deleting menu node
	 */
	public function deleteMenuItemAction()
	{
		$id = $this->getRequest()->getParam('id');

		if ($id !== null) {
			if ($this->_navigationModel->getItem($id)->read_only === true) {
				$this->_helper->getHelper('FlashMessenger')->addMessage('That was a READ ONLY navigation item');
				return $this->_helper->redirector->gotoUrl(
				    $this->view->url( array('action' => 'list-menu') )
                );
			}
		} else {
			$this->_helper->getHelper('FlashMessenger')->addMessage('No value for ID parameter');
			return $this->_helper->redirector->gotoUrl( $this->view->url( array('action' => 'list-menu') ) );
		}

		$this->_navigationModel->deleteItem($id);

		return $this->_helper->redirector->gotoUrl(
		    $this->view->url( array('action' => 'list-menu') )
		);
	}
}