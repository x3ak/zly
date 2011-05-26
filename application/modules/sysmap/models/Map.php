<?php
/**
 * Slys
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */

/**
 * Sysmap model class. Provides as singleton
 * @throws Zend_Exception
 */

namespace Sysmap\Model;

class Map
{
    /**
     * @var Sysmap_Model_Map
     */
    protected static $_instance = null;

    /**
     * @var bool
     */
    protected $_reindexed = false;

    /**
     * @var array
     */
    protected $_requestActiveElementsCache = array();

    /**
     *
     * @var \Zend\Cache\Frontend\Core
     */
    protected $_cache = null;
    
    protected $_cachedControllers = array();
    /**
     *
     * @var \DOMDocument
     */
    protected $_sysmap = null;
    
    protected $_actionSuffix = 'Action';
    
    protected $_paramFormTag = 'ParamsForm';
    
    protected function __construct()
    {
        $cache = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getBroker()
                    ->load('cachemanager')->getCacheManager();
        
        if($cache->hasCache('sysmap')) {
            $this->_cache = $cache->getCache('sysmap');            
        } else {
            throw 'Sysmap module require own cache';
        }

    }

    public static function getInstance()
    {
        if (self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * Convert MCA reprezentation for Sysmap
     *
     * @param $mca array
     * @return string
     */
    public function formatMcaName(array $mca)
    {
        return strtolower(
            (empty($mca['module']) ? '*' : $mca['module'])
            . '.' .
            (empty($mca['controller']) ? '*' : $mca['controller'])
            . '.' .
            (empty($mca['action']) ? '*' : $mca['action'])
        );
    }

    /**
     * Generate hash for item and set the value to the hash member
     * @param Sysmap_Model_Mapper_Sysmap $item
     * @return void
     */
    protected function _generateHash(\Sysmap\Model\Mapper\Sysmap $item)
    {
        if (empty($item) === false)
            $item->hash = $item->level . '-' . md5(
                $item->mca . '#' .
                $item->form_name . '#' .
                print_r($item->params, true)
            );
    }

    /**
     * Parse MCA format to array with module-controller-action
     * @return array|null
     */
    public function parseMcaFormat($mca)
    {
        if (empty($mca) === true or is_string($mca) === false)
            return null;

        $parts = explode('.', $mca);
        if (count($parts) < 3)
            return null;

        $return['module'] = $parts[0] == '*' ? 'default' : $parts[0];
        $return['controller'] = $parts[1] == '*' ? 'index' : $parts[1];
        $return['action'] = $parts[2] == '*' ? 'index' : $parts[2];

        return $return;
    }

    /**
     * Returns formatted absolute path to the
     *
     * Returns false if path can't be found
     *
     * @throws Zend_Exception
     * @param string $mca
     * @return string|bool
     */
    public function formatPathFromMca($mca)
    {
        if (empty($mca) === true or is_string($mca) === false)
            return null;

        if($mca == '*.*.*')
            return false;

        $mcaParts = explode('.',$mca);
        $mcaParts['module'] = ($mcaParts[0] == '*') ? NULL : $mcaParts[0];
        $mcaParts['controller'] = ($mcaParts[1] == '*') ? NULL : $mcaParts[1];
        $mcaParts['action'] = ($mcaParts[2] == '*') ? NULL : $mcaParts[2];

        if($mca != $this->formatMcaName($mcaParts))
            throw new Zend_Exception('Invalid MCA provided');

        $applicationPath = str_replace('\\','/',realpath(APPLICATION_PATH));

        if(!empty($mcaParts['controller'])) {
            $frontController = \Zend\Controller\Front::getInstance();
            $controllerClassName = $frontController->getDispatcher()->formatControllerName($mcaParts['controller']);
            $controllerFileName = $frontController->getDispatcher()->classToFilename($controllerClassName);

            return str_replace(
                $applicationPath,
                '',
                str_replace('\\','/',realpath(\Zend\Controller\Front::getInstance()->getControllerDirectory($mcaParts['module']).DIRECTORY_SEPARATOR.$controllerFileName))
            );
        } else {
            return str_replace($applicationPath,'',str_replace('\\','/',realpath(\Zend\Controller\Front::getInstance()->getModuleDirectory($mcaParts['module']))));
        }
    }

    /**
     * @param  $moduleName
     */
    public function addModule($moduleName, $path, $title = null, $description = null)
    {
        $rootNode = \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('mca','*.*.*');

        if (empty($rootNode))
            throw new Zend_Exception('Can not find root of the sysmap!');

        $newItem = false;
        $mapItem = \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName)));

        if (empty($mapItem)) {
            $newItem = true;
            $mapItem = new Sysmap_Model_Mapper_Sysmap();
            $mapItem->mca = $this->formatMcaName(array('module' => $moduleName));
        }

        $mapItem->title = empty($title) ? $mapItem->mca : $title;
        $mapItem->description = $description;
        $mapItem->path = dirname($path);
        $this->_generateHash($mapItem);
        $mapItem->save();

        if ($newItem) {
            if (empty($mapItem) === false) {
                $mapItem->getNode()->insertAsLastChildOf($rootNode);
                $this->_generateHash($mapItem);
                $mapItem->save();
            }
            else
                throw new Zend_Exception('Unable to assign new item to not existing root element!');
        }
    }

    /**
     * @param  $moduleName
     * @param  $controllerName
     */
    public function addController($moduleName, $controllerName, $path, $title = null, $description = null)
    {
        $moduleRoot = \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName)));

        if (empty($moduleRoot))
            throw new Zend_Exception('Can not find module root entry!');

        $newItem = false;
        $mapItem = \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName)));

        if (empty($mapItem)) {
            $newItem = true;
            $mapItem = new Sysmap_Model_Mapper_Sysmap();
            $mapItem->mca = $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName));
        }

        $mapItem->title = empty($title) ? $mapItem->mca : $title;
        $mapItem->description = $description;
        $mapItem->path = $path;
        $this->_generateHash($mapItem);
        $mapItem->save();

        if ($newItem) {
            if (empty($mapItem) === false) {
                $mapItem->getNode()->insertAsLastChildOf($moduleRoot);
                $this->_generateHash($mapItem);
                $mapItem->save();
            }
            else
                throw new Zend_Exception('Unable to assign new item to not existing root element!');
        }
    }

    /**
     * @param  $moduleName
     * @param  $controllerName
     * @param  $actionName
     */
    public function addAction($moduleName, $controllerName, $actionName, $path, $formClass = null, $title = null, $description = null)
    {
        $controllerRoot = \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName)));

        if (empty($controllerRoot))
            throw new Zend_Exception('Can not find controller root element!');

        $newItem = false;
        $mapItem = \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('mca', $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName, 'action' => $actionName)));

        if (empty($mapItem)) {
            $newItem = true;
            $mapItem = new Sysmap_Model_Mapper_Sysmap();
            $mapItem->mca = $this->formatMcaName(array('module' => $moduleName, 'controller' => $controllerName, 'action' => $actionName));
        }

        $mapItem->form_name = $formClass;
        $mapItem->title = empty($title) ? $mapItem->mca : $title;
        $mapItem->description = $description;
        $mapItem->path = $path;
        $this->_generateHash($mapItem);
        $mapItem->save();

        if ($newItem) {
            if (empty($mapItem) === false) {
                $mapItem->getNode()->insertAsLastChildOf($controllerRoot);
                $this->_generateHash($mapItem);
                $mapItem->save();
            }
            else
                throw new Zend_Exception('Unable to assign new item to not existing root element!');
        }
    }

    /**
     * Returns nested set of the sysmap
     * @param null $fields
     * @return Doctrine_Tree
     */
    public function getMapTree($fields = null)
    {
        $tree = Doctrine_Core::getTable('Sysmap_Model_Mapper_Sysmap')->getTree();
    	$baseAlias = $tree->getBaseAlias();
    	$select = '';

    	if ($fields === null)
    		$select = $baseAlias . '.id,' . $baseAlias . '.title';
    	else {
    		foreach ($fields as $field)
    			$select .= $baseAlias . '.' . $field . ',';

    		$select = substr($select, 0, -1);
    	}

		$tree->setBaseQuery(
			Doctrine_Core::getTable('Sysmap_Model_Mapper_Sysmap')
						   ->createQuery($baseAlias)
						   ->select($select)
		);

    	return $tree;
    }

    /**
     * Converts camel-case method name to dashed url version (someActionTestAction to some-action-test)
     *
     * @param  string $actionName
     * @return string
     */
    protected function _urlActionName($actionName)
    {
        return strtolower( preg_replace('/([A-Z]+)/', '-\1', preg_replace('/(Action)$/', '', $actionName) ) );
    }

    /**
     * $paths variable can be DirectoryIterator object
     * or an array of DirectoryIterator objects
     *
     * @param DirectoryIterator|array $paths
     * @return void
     */
    public function addToMap($paths)
    {
        if (empty($paths))
            return;

        if ( is_array($paths) === false )
            $paths = array($paths);

        $mapMethods = array();

        foreach($paths as $path) {
            require_once APPLICATION_PATH. $path;
            $reflectionFile = new Zend_Reflection_File(APPLICATION_PATH . $path);
            $class = $reflectionFile->getClass();

            $classParts = explode('_', $class->getName());

            $module = 'default';

            if (count($classParts) == 1)
                $controllerName = strtolower(str_replace('Controller', '', $classParts[0]));
            else {
                $module = $classParts[0];
                $controllerName = strtolower(str_replace('Controller', '', $classParts[1]));
            }

            $this->addModule($module, $path);

            $controllerDocTitle = $controllerName;
            $controllerDocDescription = '';

            try {
                $controllerDoc = $class->getDocblock();

                $controllerDocTitle = $controllerDoc->getShortDescription();
                $controllerDocDescription = $controllerDoc->getLongDescription();
            }
            catch(Zend_Reflection_Exception $exception) {
            }

            // adding module-controller
            $this->addController($module, $controllerName, $path, $controllerDocTitle, $controllerDocDescription);

            $tmpMapMethods = \Sysmap\Model\DbTable\Sysmap::getInstance()->findActions($module, $controllerName, array(), Doctrine_Core::HYDRATE_ARRAY);

            foreach($tmpMapMethods as $method)
                $mapMethods[$method['mca']] = $method;

            unset($tmpMapMethods);

            foreach ($class->getMethods() as $method) {
                $methodName = $method->getName();

                if (!preg_match('/.+Action$/', $methodName))
                    continue;

                $title = '';
                $formClass = '';
                $description = '';

                try {
                    $docBlock = $method->getDocblock();
                    $formTag = $docBlock->getTag('paramsform');

                    if (!empty($formTag))
                        $formClass = trim($formTag->getDescription());

                    $title = trim($docBlock->getShortDescription());
                    $description = trim($docBlock->getLongDescription());
                }
                catch(Zend_Reflection_Exception $exception) {
                }

                // add module-controller-action + form name
                $this->addAction($module, $controllerName, $this->_urlActionName($methodName), $path, $formClass, $title, $description);

                $mcaKey = $this->formatMcaName(array('module' => $module, 'controller' => $controllerName, 'action' => $this->_urlActionName($methodName)));
                if (isset($mapMethods[$mcaKey]))
                    unset($mapMethods[$mcaKey]);
            }

            if (empty($mapMethods) === false) {
                \Sysmap\Model\DbTable\Sysmap::getInstance()->deleteRecords(array_values($mapMethods));
                unset($mapMethods);
            }
        }
    }

    /**
     * Reindex MCA
     * @return void
     */
    public function reindexMCA()
    {
        $map = array();
        $curContrl = $this->_getCurrentApplicationControllers();
        $prevContrl = $this->_getPreviousApplicationControllers();
        
        foreach($curContrl as $hash=>$file) {
            if(!array_key_exists($hash, $prevContrl)) {
                $ctrlInfo = $this->_saveControllerMap($file['file']);
                $map[$file['module']][] = $ctrlInfo;
            }
        }
        \Zend\Debug::dump($map);
        $this->_cache->save($curContrl, 'controllers');
        die('debug reindex MCA');
    }
    
    /**
     * Return current hashes of controllers files
     * 
     * @return array
     */
    protected function _getCurrentApplicationControllers() 
    {
        if(!empty($this->_controllers) && is_array($this->_controllers))
                return $this->_controllers;
        
        $controllers = array();
        $controllersDirs = \Zend\Controller\Front::getInstance()->getControllerDirectory();
        foreach($controllersDirs as $module=>$dir) {
            $dirIterator = new \DirectoryIterator($dir);
            foreach ($dirIterator as $file) {
                if($file->isFile())
                    $controllers[hash_file('md5', $file->getPathname())] = 
                            array('module'=>$module,'file'=>$file->getPathname());
                    
            }
        }
        $this->_controllers = $controllers;
        return $controllers;
    }
    
    /**
     * Return previos hashes of controllers files
     * 
     * @return array 
     */
    protected function _getPreviousApplicationControllers()
    {
        $controllers = array();
        if($this->_cache->test('controllers')) {
            $controllers = (array)$this->_cache->load('controllers');
        }
        return $controllers;
    }
    
    /**
     * Save file methods information to sysmap cache
     * 
     * @param string $fileName 
     */
    protected function _saveControllerMap($fileName)
    {
        include_once $fileName;
        $file = new \Zend\Reflection\ReflectionFile($fileName);
        $classes = $file->getClasses();
        $controller = array();
        foreach ($classes as $class) {
            if('' != $class->getDocComment()) {
                $controller['longDescr'] = $class->getDocblock()->getLongDescription();
                $controller['shortDescr'] = $class->getDocblock()->getShortDescription();
            }
            $controller['name'] = $class->getName();
            $actions = array();
            foreach($class->getMethods() as $method) {
                
                $methodName = $method->getName();
                
                if(strstr($methodName, $this->_actionSuffix)) {
                    $action = array();  
                    $action['name'] = $methodName;
                    if('' != $method->getDocComment()) {
                        $docBlock = $method->getDocblock();
                        $action['shortDescr'] = $docBlock->getShortDescription();
                        $action['longDescr'] = $docBlock->getLongDescription();
                        if($docBlock->hasTag($this->_paramFormTag)) {
                            $action['paramForm'] = $docBlock->getTag($this->_paramFormTag)->getDescription();
                        }
                    }
                    $actions[] = $action;
                }
                
                
            }                
            $controller['actions'] = $actions;            
        }
        
        return $controller;

    }

    public function addExtend(array $data)
    {
        if (empty($data) === true)
            throw new Zend_Exception('Can not create an extend! Empty data passed!');

        $mapItem = \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('id', $data['sysmap_id']);

        if (empty($mapItem))
            throw new Zend_Exception('The root element you choosed does not exists!');

        if ($mapItem->level != 3)
            throw new Zend_Exception('You can assign extend only to the map item with level equal 3 (to actions)!');

        if (empty($mapItem->form_name) === true)
            throw new Zend_Exception('You can not create extend from action without form!');

        $extend = new Sysmap_Model_Mapper_Sysmap();

        if (empty($data['id']) === false)
            $extend->assignIdentifier($data['id']);

        unset($data['id']);

        $extend->fromArray($data);
        $extend->save();

        $extend->getNode()->insertAsLastChildOf($mapItem);

        $extend->mca = $mapItem->mca;
        $this->_generateHash($extend);
        $extend->mca = null;
        $extend->save();
    }

    /**
     * Return item or null by $hash
     * @param  $hash
     * @return Doctrine_Record
     */
    public function getItemByHash($hash)
    {
        if (empty($hash) === false)
            return \Sysmap\Model\DbTable\Sysmap::getInstance()->findOneBy('hash', $hash);

        return null;
    }

    /**
     * Returns form tree element with filled map values
     * It automatically makes reindex to provide fresh information
     *
     * @return Slys_Form_Element_Tree
     */
    public function getMapTreeElement()
    {
        $this->reindexMCA();

        $sysmapTree = $this->getMapTree(array('id', 'title', 'hash', 'mca', 'level'))->fetchTree(array('id' => 1), Doctrine_Core::HYDRATE_ARRAY_HIERARCHY);

        $tree = new Slys_Form_Element_Tree('sysmap_id');
        $tree->setValueKey('hash')
             ->setTitleKey('title')
             ->setAllowEmpty(false)
             ->setRequired(true);

        $tree->setLabel('sysmap_tree');
        $tree->addMultiOptions($sysmapTree);

        return $tree;
    }

    /**
     * Return currently active sysmap items based on current request or request passed as a parameter
     * @param null|Zend_Controller_Request_Abstract $customRequest
     * @return null|Doctrine_Collection
     */
    public function getActiveItems(Zend_Controller_Request_Abstract $customRequest = null)
    {
        $collection = null;

        if ($customRequest !== null)
            $request = $customRequest;
        else
            $request = \Zend\Controller\Front::getInstance()->getRequest();

        if (empty($request))
            return null;

        $currentMcaName = $this->formatMcaName(array(
            'module' => $request->getModuleName(),
            'controller' => $request->getControllerName(),
            'action' => $request->getActionName()
        ));

        if (empty($this->_requestActiveElementsCache[$currentMcaName]) === false)
            return $this->_requestActiveElementsCache[$currentMcaName];

        $this->reindexMCA();

        $currentMcaCollection = \Sysmap\Model\DbTable\Sysmap::getInstance()->findBy(
            'mca',
            $this->formatMcaName(array(
                'module' => $request->getModuleName(),
                'controller' => $request->getControllerName(),
                'action' => $request->getActionName()
            ))
        );

        $mapItem = $currentMcaCollection[0];

        if (empty($mapItem) === false) {
            $collection = $mapItem->getNode()->getAncestors();

            if (empty($collection) === false)
                $collection->add($mapItem);
            else
                $collection = $currentMcaCollection;
        }

        $this->_requestActiveElementsCache[$currentMcaName] = $collection;

        return $collection;
    }

    public function getItemParentsByHash($hash)
    {
        $currentCollection = \Sysmap\Model\DbTable\Sysmap::getInstance()->findBy('hash', $hash);
        $mapItem = $currentCollection[0];

        if (empty($mapItem) === false) {
            $collection = $mapItem->getNode()->getAncestors();

            if (empty($collection) === false)
                $collection->add($mapItem);
            else
                $collection = $currentCollection;
        }
        else
            $collection = $currentCollection;

        return $collection;
    }
    
    /**
     * Returns root element from map.
     * @return \DOMElement
     */
    public function getRootElement()
    {
        if(empty($this->_sysmap))
                return false;
        
        return $this->_sysmap->getElementsByTagName('root');
    }

    /**
     * Finds modules in in map.
     *
     * @param array $params              query parameters (a la PDO)
     * @param int $hydrationMode         Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Collection|array Depends from $hydrationMode can be collection of Sysmap_Model_Mapper_Sysmap
     */
    public function findModules($params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Sysmap_Model_Mapper_Sysmap')
            ->where('level < 2')
            ->execute($params,$hydrationMode);
    }

    /**
     * Finds controllers in in map.
     *
     * @param array $params              query parameters (a la PDO)
     * @param int $hydrationMode         Doctrine_Core::HYDRATE_ARRAY or Doctrine_Core::HYDRATE_RECORD
     * @return Doctrine_Collection|array Depends from $hydrationMode can be collection of Sysmap_Model_Mapper_Sysmap
     */
    public function findControllers($params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Sysmap_Model_Mapper_Sysmap')
            ->where('level = 2')
            ->execute($params,$hydrationMode);
    }

    /**
     * Gets the list of all actions for specified module-controller
     * @param  $moduleName
     * @param  $controllerName
     * @param  array $params
     * @param  null $hydrationMode
     * @return Doctrine_Collection
     */
    public function findActions($moduleName, $controllerName, $params = array(), $hydrationMode = null)
    {
        return Doctrine_Query::create()
            ->select()
            ->from('Sysmap_Model_Mapper_Sysmap')
            ->where('mca like ?', $moduleName.'.'.$controllerName.'.%')
            ->andWhere('level = 3')
            ->execute($params,$hydrationMode);
    }

    /**
     * Clear records with the passed id(s)
     * @param  string|array $ids
     * @return void
     */
    public function deleteRecords($ids)
    {
        if (is_string($ids))
            $ids = array(array('id' => $ids));

        foreach($ids as $id)
            $this->findOneBy('id', $id['id'])->getNode()->delete();
    }
}