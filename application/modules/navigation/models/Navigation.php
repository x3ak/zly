<?php
/**
 * Zly
 *
 * Main module navigation module
 *
 * @author     Serghei Ilin <criolit@gmail.com>
 * @version    $Id: Navigation.php 1176 2011-02-04 16:05:59Z criolit $
 */
namespace Navigation\Model;

class Navigation extends \Zly\Doctrine\Model
{
	/**
	 * Defines that navigation item referers to an external resource
	 * @var string
	 */
    const TYPE_EXTERNAL = 'external';

    /**
     * Defines that navigation type will contain controller and action keys in array of response
     * @var string
     */
    const TYPE_PROGRAMMATIC = 'programmatic';

    /**
     * Defines that navigation type is root of the navigation
     * @var string
     */
    const TYPE_NAVIGATION_ROOT = 'menu';

    /**
     * @var boolean
     */
    protected $_cacheEnabled = false;

    /**
     * @var Zend_Cache_Core
     */
    protected $_cache = null;

    /**
     * @var string
     */
    protected $_cacheName = 'navigation_full_navigation';

    public function __construct()
    {
        $options = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getResource('modules')->navigation->getOptions();

        if (empty($options) === false) {
            $this->_cacheEnabled = (boolean)$options['cache']['enabled'];

            $this->_cache = \Zend\Cache\Cache::factory(
                $options['cache']['frontend']['name'],
                $options['cache']['backend']['name'],
                $options['cache']['frontend']['options'],
                $options['cache']['backend']['options']
            );
        }
    }
    
    public function initSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);    
        $tool->createSchema($classes);
        return $this;
    }
    
    public function updateSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->updateSchema($classes);
        return $this;
    }
    
    public function dropSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);
        return $this;
    }
    
    protected function _getShemaClasses()
    {
        $em = $this->getEntityManager();
        $classes = array(
          $em->getClassMetadata('\Navigation\Model\Mapper\Item')
        );
        
        return $classes;
    }
    
    public function createRoot()
    {
        $node = new Mapper\Item();
        $node->setTitle('Root');
        $node->setType(self::TYPE_NAVIGATION_ROOT);
        return $this->getEntityManager()->getRepository('\Navigation\Model\Mapper\Item')->createRoot($node);
    }

    /**
     * Returns NestedSet with all navigation items
     *
     * @param array $fields List of table fields
     * @return \Zly\Doctrine\NestedSet\Node
     */
    public function getStructureTree($fields = null)
    {
    	$tree = $this->getEntityManager()->getRepository('\Navigation\Model\Mapper\Item')->getTree();

    	return $tree;
    }

    /**
     * Get navigation item
     * @param int $id
     * @return \Navigation\Model\Mapper\Item
     */
    public function getItem($id)
    {
    	if ($id === null)
    		return null;

    	return $this->getEntityManager()->getRepository('\Navigation\Model\Mapper\Item')->find($id);
    }

    /**
     * Save normal item in navigation tree
     * @param array $values
     */
    public function saveLeafItem(array $values)
    {
        if (empty($values))
            return ;

        $newNode = true;

        $childNode = new \Navigation\Model\Mapper\Item();
        $rootNode = $this->getEntityManager()->getRepository('\Navigation\Model\Mapper\Item')->getTree($values['parent_id']);

        if (!empty($values['id'])) {
            $childNode->assignIdentifier($values['id']);
            $newNode = false;
        }

        unset($values['id']);

        if (empty($values['options']) === false) {
            foreach($values['options'] as $key => $value)
                $values[$key] = $value;

            unset($values['options']);
        }

        $childNode->fromArray($values);
        $node = $this->getEntityManager()->getRepository('\Navigation\Model\Mapper\Item')->wrapNode($childNode);
        $childNode = $node->getNode();
        $this->getEntityManager()->persist($childNode);
        $this->getEntityManager()->flush();

        if ($newNode === true)
            $node->insertAsLastChildOf($rootNode);
        else
            $node->moveAsLastChildOf($rootNode);

        if ($this->_cacheEnabled)
            $this->_cache->remove($this->_cacheName);
    }

    /**
     * Delete menu node
     * @param $id
     */
    public function deleteItem($id)
    {
    	if (!empty($id)) {
			$menu = $this->getEntityManager()->getRepository('\Navigation\Model\Mapper\Item')->findOneById($id);
			$menu->getNode()->delete();

            if ($this->_cacheEnabled)
                $this->_cache->remove($this->_cacheName);
		}
    }

    /**
     * Get user defined navigation
     * @param int $itemId If null is passed all menus will be as one menu
     * @return \Zend\Navigation\Navigation
     */
    public function getNavigation($itemId = null)
    {   
        if ($this->_cache->test($this->_cacheName) === false) {
            $navigation = new \Zend\Navigation\Navigation();

            $tree = $this->getEntityManager()->getRepository('\Navigation\Model\Mapper\Item')->getTreeAsArray();
            
            if(empty($tree))
                return false;

            $this->_formatNavigationPages($tree, $navigation);

            if ($this->_cacheEnabled)
                $this->_cache->save($navigation, $this->_cacheName);
        }
        else
            $navigation = $this->_cache->load($this->_cacheName);

        if ($itemId !== null) {
            $page = $navigation->findOneBy('id', $itemId);

            $navigation = new \Zend\Navigation\Navigation();

            if ($page !== false)
                $navigation->addPage($page);
        }

		return $navigation;
    }

    /**
     * Gets all user defined navigation
     * @param array $root First node for the current tree
     * @param \Zend\Navigation\Container $navigation Navigation object which will contain navigation converted from
     * NestedSet
     */
    protected function _formatNavigationPages($root, \Zend\Navigation\Container $navigation)
    {
        /** @var $item \Navigation\Model\Mapper\Item */
    	foreach ($root as $node) {
                $page = null;
                $item = $node->getNode();
    		if ($item->getType() == self::TYPE_EXTERNAL) {
    			$page = new \Zend\Navigation\Page\Uri();

    			$page->id = $item->getId();
    			$page->label = $item->getTitle();
    			$page->uri = $item->getExternalLink();
    		}
    		elseif ($item->getType() == self::TYPE_PROGRAMMATIC) {
                $page = new \Zend\Navigation\Page\Mvc();

                $page->id = $item->getId();
                $page->label = $item->getTitle();
                $page->route = $item->getRoute();

                if ($page->reset_params === null)
                    $page->reset_params = true;

                /** @var $sysmapItem Sysmap_Model_Mapper_Sysmap */
                $sysmapItem = \Zly\Api\ApiService::getInstance()->request(
                    new \Zly\Api\Request($this, 'sysmap.get-item-by-identifier', array(
                        'identifier' => $item->getSysmapIdentifier()
                    ))
                );

				if (empty($sysmapItem) === true)
					continue;

                $sysmapItem = $sysmapItem->getFirst();

                if (empty($sysmapItem) === true)
					continue;

                $mca = $sysmapItem->toRequest();

                $page->module = $mca->getModuleName();
                $page->controller = $mca->getControllerName();
                $page->action = $mca->getActionName();

                $params = $mca->getParams();
                if (!empty($params))
                    $page->params = $mca->getParams();
            }
            elseif($item->getType() == self::TYPE_NAVIGATION_ROOT) {
                $page = new \Zend\Navigation\Page\Uri();
    			$page->id = $item->getId();
    			$page->label = $item->getTitle();
    			$page->uri = '/';
            }

            if ($page === null)
                return;

            $itemNode = $node;

            if ($itemNode->hasChildren())
                $this->_formatNavigationPages($itemNode->getChildren(), $page);

            $navigation->addPage($page);
    	}
    }

    /**
	 * Create one navigation container from Zend_Navigation_Page_Mvc objects
	 *
	 * @param array $navigations Should contain Zend_Navigation_Page_Mvc objects
	 * @return Zend_Navigation|null
	 */
	public function mergeNavigations($navigations)
	{
		if (!is_array($navigations) or empty($navigations))
			return null;

		$completeNavigation = new \Zend\Navigation\Navigation();

		foreach ($navigations as $navigation) {
			if ($navigation instanceof \Zend\Navigation\Page) {
				$completeNavigation->addPage($navigation);
			}
		}

		return $completeNavigation;
	}

    /**
	 * Returns Zend_Navigation with pages which have setted conditions
	 * $conditions have to have the following structure:
	 * [index][page_property] = [page_value]
	 *
	 * @param \Zend\Navigation\Navigation $navigation
	 * @param array $conditions
	 * @return \Zend\Navigation\Navigation|null
	 */
	public function getPagesByConditions(\Zend\Navigation\Navigation $navigation, $conditions, $leaveParents = false)
	{
		if (!is_array($conditions) or empty($conditions))
			return null;

		$resultNavigation = new \Zend\Navigation\Navigation();

		$iterator = new RecursiveIteratorIterator($navigation, RecursiveIteratorIterator::SELF_FIRST);
		// iterating over the navigation pages
		foreach ($iterator as $page) {
			// iterating over the conditions
			foreach ($conditions as $index => $condition) {
				$matched = true;
				// iterating over the condition properties
				foreach ($condition as $property => $value) {
					if (is_array($value)) {
						if (!isset($page->$property) or !in_array($page->$property, $value)) {
							$matched = false;
							break;
						}
					}
					elseif (!isset($page->$property) or $page->$property != $value) {
						$matched = false;
						break;
					}
				}

				if ($matched === true) {
					$resultPage = clone $page;

					if ($leaveParents === false) {
						$resultPage = $resultPage->toArray();
						unset($resultPage['pages']);

						$resultPage = \ZendNavigation\Page::factory($resultPage);
					}

					$resultNavigation->addPage($resultPage);
				}
			}
		}

		return $resultNavigation;
	}
}