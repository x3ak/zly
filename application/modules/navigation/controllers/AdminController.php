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
		$this->_navigationModel = Navigation_Model_Navigation::getInstance();
	}

    /**
     *
     * @return void
     */
	public function indexAction()
	{
		$this->_forward('list-menu');
	}

	/**
	 * List structured menu
	 */
	public function listMenuAction()
	{
		$this->view->tree = array(
            $this->_navigationModel->getStructureTree(array('id', 'title', 'route', 'read_only'))->fetchTree(array(), Doctrine_Core::HYDRATE_ARRAY_HIERARCHY)
        );
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
				$this->_helper->getHelper('FlashMessenger')->addMessage('That was a READ ONLY navigation item');
				return $this->_helper->redirector->gotoUrl(
				    $this->view->url( array('action' => 'list-menu') )
                );
			}
		}

		$form = new Navigation_Form_MenuItem();

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->getRequest()->getPost())) {
				$this->_navigationModel->saveLeafItem( $form->getValues() );
				return $this->_helper->redirector->gotoUrl(
				    $this->view->url( array('action' => 'list-menu', 'module' => 'navigation', 'controller' => 'admin'), null, true )
                );
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
                    return $this->_helper->redirector->gotoUrl(
				        $this->view->url( array('action' => 'edit-menu-item', 'module' => 'navigation', 'controller' => 'admin'), null, true )
                    );
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
		}
		else {
			$this->_helper->getHelper('FlashMessenger')->addMessage('No value for ID parameter');
			return $this->_helper->redirector->gotoUrl( $this->view->url( array('action' => 'list-menu') ) );
		}

		$this->_navigationModel->deleteItem($id);

		return $this->_helper->redirector->gotoUrl(
		    $this->view->url( array('action' => 'list-menu') )
		);
	}
}