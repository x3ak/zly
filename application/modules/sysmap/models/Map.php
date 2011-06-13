<?php
/**
 * Slys 2
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */

/**
 * Sysmap model class
 */

namespace Sysmap\Model;

class Map
{    
    /**
     *
     * @var \Zend\Cache\Frontend\Core
     */
    protected $_cache = null;
    
    protected $_cachedControllers = array();

    static protected $_sysmap = null;
    
    protected $_actionSuffix = 'Action';
    
    protected $_paramFormTag = 'Qualifier';
    
    protected $_reindexing = false;
    
    protected $_configFile;
    
    public function __construct()
    {
        $cache = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getBroker()
                    ->load('cachemanager')->getCacheManager();
        
        if($cache->hasCache('sysmap')) {
            $this->_cache = $cache->getCache('sysmap');            
        } else {
            throw 'Sysmap module require own cache';
        }

    }

    /**
     * Return structured system map
     * @return array 
     */
    public function getSysmap()
    {
        if(!empty(self::$_sysmap))
            return self::$_sysmap;
        
        if(APPLICATION_ENV == 'development') {
            return $this->_reindexMCA();
        } elseif($map = $this->_loadMapCache()) {
            return $map;
        } else {
            return false;
        }
    }
    
    /**
     * Return currently active sysmap items based on current request or request passed as a parameter
     * @param null|Zend_Controller_Request_Abstract $customRequest
     * @return null|Doctrine_Collection
     */
    public function getActiveItems(\Zend\Controller\Request\AbstractRequest $customRequest = null)
    {   
        $request = \Zend\Controller\Front::getInstance()->getRequest();
        if(!empty($customRequest)) {
            $request = $customRequest;
        }
        
        $activeItems = array();
        
        $sysmap = $this->getSysmap();

        $module = $sysmap[$request->getModuleName()];
        $controller = $module->controllers[$request->getControllerName()];
        $action = $controller->actions[$request->getActionName()];
        
        $activeItems[0] = new \Zend\Acl\Resource\GenericResource(md5('*.*.*'));
        $activeItems[1] = new \Zend\Acl\Resource\GenericResource($module->hash);
        $activeItems[2] = new \Zend\Acl\Resource\GenericResource($controller->hash);
        $activeItems[3] = new \Zend\Acl\Resource\GenericResource($action->hash);
        
        $extHashes = $this->_getExtensionsByRequest($action->hash, $request->getParams(), true);
        foreach($extHashes as $hash)
            $activeItems[] = new \Zend\Acl\Resource\GenericResource($hash);
        
        return $activeItems;
    }
    
    /**
     * Return extension object by hash
     * @param string $hash
     * @return stdClass
     */
    public function getNodeByHash($hash)
    {
        $sysmap = $this->getSysmap();
        foreach($sysmap as $module) {
            
            if($module->hash == $hash) {
                return $module;
            }
            
            foreach($module->_childrens as $controller) {
                
                if($controller->hash == $hash) {
                    return $controller;
                }
                
                foreach($controller->_childrens as $action) {
                    
                    if($action->hash == $hash) {
                        return $action;
                    }
                    
                    foreach($action->_childrens as $ext) {
                        if($ext->hash == $hash) {
                            return $ext;
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Return parent of extension by extension hash
     * @param string $hash
     * @return stdClass
     */
    public function getParentByHash($hash)
    {
        $sysmap = $this->getSysmap();
        foreach($sysmap as $module) {
            foreach($module->_childrens as $controller) {
                
                if($controller->hash == $hash) {
                    return $module;
                }
                
                foreach($controller->_childrens as $action) {
                    if($action->hash == $hash) {
                        return $controller;
                    }
                    foreach($action->_childrens as $ext) {
                        if($ext->hash == $hash) {
                            return $action;
                        }
                    }
                }
            }
        }        
    }
    
    /**
     * Return from element with system map tree
     * @return \Slys\Form\Element\Tree 
     */
    public function getMapTreeElement()
    {
        $sysmap = $this->getSysmap();

        $formElement = new \Slys\Form\Element\Tree('sysmap_id');
        $formElement->setValueKey('hash');
        $formElement->setTitleKey('name');
        $formElement->setChildrensKey('_childrens');
        $formElement->setMultiOptions($sysmap);
        return $formElement;
    }
    
    /**
     * Save extension to application local storage
     * @param array $values 
     */
    public function saveExtension($values)
    {
        $config = $this->_loadLocalConfig();
        $parent = $this->getParentByHash($values['hash']);
        
        if(!empty($parent)) {
            foreach($config as $section) {

                if(!empty($section->sysmap->extensions->{$parent->hash}->{$values['hash']})) {
                    unset($section->sysmap->extensions->{$parent->hash}->{$values['hash']});
                }
            }
        }
        
        if(!empty($values['params'])) {
            $curHash = md5(implode('&',$values['params']));
            $options = array(
                APPLICATION_ENV => array(
                'sysmap'=>array(
                    'extensions'=>array(
                        $values['sysmap_id']=>array(
                            $curHash => array(
                            'name' => $values['name'],
                            'params'=>$values['params']))))));
            
            $prevConfig = $config->toArray();
            $merged = array_merge_recursive($prevConfig, $options);            
            $config = new \Zend\Config\Config($merged);
        }
        
        $this->_saveConfig($config);
        return true;
    }
    
    public function deleteExtend($hash)
    {
        $config = $this->_getConfig();
        foreach($config->extensions as $extension) {
            foreach($extension as $module) {
                foreach($module as $controller) {
                    
                }
            }
        }
        return $this;
    }
    
    /**
     * Return extension hash of provided action and request
     * 
     * @param string $actionHash
     * @param \Zend\Controller\Request\AbstractRequest $request
     * @return array
     */
    protected function _getExtensionsByRequest($hash, $currentParams = array(), $hashesOnly = false)
    {
        $options = $this->_loadLocalConfig(APPLICATION_ENV);

        if(!empty($options->sysmap->extensions->{$hash})) {
            $extensions = $options->sysmap->extensions->{$hash};
                
            $currentExtensions = array();

            foreach($extensions as $hash=>$extendParams) {
                
                $toOutput = true;
                
                if(!empty($request)) {
                    foreach($extendParams['params'] as $key=>$value) {
                        if(!isset($currentParams[$key]) 
                                || (isset($currentParams[$key]) && $currentParams[$key] != $value)) {
                            $toOutput = false;
                        }
                    }
                }
                if($toOutput) {
                    if($hashesOnly)
                        $currentExtensions[$hash] = $hash;
                    else {
                        $extObject = new \stdClass();
                        $extObject->name = $extendParams->name;
                        $extObject->params = $extendParams->params;
                        $extObject->hash = $hash;
                        $extObject->level = 4;
                        $currentExtensions[$hash] = $extObject;
                    }
                }

            } 
            return $currentExtensions;
        }
        return array();
    }
    
    /**
     * Return hash of current request
     * 
     * @param \Zend\Controller\Request\AbstractRequest $request
     * @return string
     */
    protected function _getHashByRequest(\Zend\Controller\Request\AbstractRequest $request)
    {
        $mca = "{$request->getModuleName()}.{$request->getControllerName()}.{$request->getActionName()}";
        $params = $request->getParams();
        if(!empty($params)) {
            $params = http_build_query($params);
            $mca .= ':'.$params;
        }

        return md5($mca);
    }
    
    /**
     * Reindex MCA
     * @return void
     */
    protected function _reindexMCA()
    {
        if($this->_reindexing)
            return self::$_sysmap;
        
        $this->_reindexing = true;
        
        $map = $this->_loadMapCache();
        if(empty($map))
            $map = array();

        $curContrl = $this->_getCurrentControllers();
        $prevContrl = $this->_getControllersCache();

        foreach($curContrl as $hash=>$file) {            
            if(!array_key_exists($hash, $prevContrl)) {
                $ctrlInfo = $this->_getControllerMap($file['file']);
                $module = new \stdClass();
                $module->hash = md5($file['module'].'.*.*');
                $module->level = 1;
                $module->name = $file['module'];
                $module->_childrens[$ctrlInfo->name] = $ctrlInfo;
                $map[$file['module']] = $module;
            }   
        }
        
        foreach($map as $mkey=>$module) {
            foreach($module->_childrens as $ckey=>$controller) {
                foreach($controller->_childrens as $akey=>$action) {
                   $action->_childrens = $this->_getExtensionsByRequest($action->hash);
                }
            }
        }

        $this->_saveSysmap($map);
        $this->_saveControllersCache($curContrl);
        $this->_reindexing = false;
        return $map;
    }
    
    /**
     * Return cached system map
     * @return array 
     */
    protected function _loadMapCache()
    {
        if($this->_cache->test('map'))
            return $this->_cache->load('map');
        else 
            return $this->_reindexMCA();
    }
    
    /**
     * Return current hashes of controllers files
     * 
     * @return array
     */
    protected function _getCurrentControllers() 
    {
        if(!empty($this->_controllers) && is_array($this->_controllers))
                return $this->_controllers;
        
        $controllers = array();
        $controllersDirs = \Zend\Controller\Front::getInstance()->getControllerDirectory();
        foreach($controllersDirs as $module=>$dir) {
            $dirIterator = new \DirectoryIterator($dir);
            foreach ($dirIterator as $file) {
                if($file->isFile()) {                    
                    $controllers[md5(filemtime($file->getPathname()).$file->getPathname())] = 
                            array('module'=>$module,'file'=>$file->getPathname());
                }
                    
            }
        }
        $this->_controllers = $controllers;
        return $controllers;
    }
    
    /**
     * Save sysmap into the cache
     * @param array $sysmap
     * @return boolean 
     */
    protected function _saveSysmap($sysmap) 
    {
        self::$_sysmap = $sysmap;
        return $this->_cache->save($sysmap, 'map');
    }
    
    /**
     * Save controllers modification info into the cache
     * @param array $controllers
     * @return boolean 
     */
    protected function _saveControllersCache($controllers) 
    {
        return $this->_cache->save($controllers, 'controllers');
    }
    
    /**
     * Return controllers modification info from the cache
     * @return array 
     */
    protected function _getControllersCache() 
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
    protected function _getControllerMap($fileName)
    {
        include_once $fileName;
        @$file = new \Zend\Reflection\ReflectionFile($fileName);
        $classes = $file->getClasses();
        $controller = new \stdClass();
        $controller->level = 2;
        
        foreach ($classes as $class) {
            if('' != $class->getDocComment()) {
                $controller->longDescr = $class->getDocblock()->getLongDescription();
                $controller->shortDescr = $class->getDocblock()->getShortDescription();
            }
            
            $toDashFilter = new \Zend\Filter\Word\CamelCaseToDash();
            $parts = explode('\\',$class->getName());
            
            if(count($parts) < 2)
                array_unshift($parts, \Zend\Controller\Front::getInstance()->getDefaultModule());
                
            list($namespace, $className) = $parts;
                
            $controller->name = strtolower($toDashFilter->filter(str_replace('Controller', '', $className)));            
            $controller->module = $namespace;            
            $controller->hash = md5($controller->module.'.'.$controller->name.'.*');
            
            $actions = array();
            foreach($class->getMethods() as $method) {
                
                $methodName = $method->getName();
                
                if(strstr($methodName, $this->_actionSuffix)) {
                    
                    $action = new \stdClass(); 
                    $action->level = 3;
                    $action->name = strtolower($toDashFilter->filter(str_replace($this->_actionSuffix, '', $methodName)));
                    $action->hash = md5($controller->module.'.'.$controller->name.'.'.$action->name);
                    
                    if('' != $method->getDocComment()) {
                        $docBlock = $method->getDocblock();
                        $action->shortDescr = $docBlock->getShortDescription();
                        $action->longDescr = $docBlock->getLongDescription();
                        if($docBlock->hasTag($this->_paramFormTag)) {
                            $action->{$this->_paramFormTag} = $docBlock->getTag($this->_paramFormTag)->getDescription();
                        }
                    }
                    
                    $actions[$action->name] = $action;
                }
            }                
            $controller->_childrens = $actions;            
        }
        
        return $controller;

    }
    
    protected function _loadLocalConfig($section = null)
    {
        $localConfigFile = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getOption('config');
        $options = array(
          'allowModifications' => true
        );
        $localConfig = new \Zend\Config\Ini($localConfigFile, $section, $options);
        return $localConfig;
    }
    
    protected function _saveConfig($config)
    {
        $localConfigFile = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getOption('config');
        $writer = new \Zend\Config\Writer\Ini();
        $writer->setFilename($localConfigFile);
        $writer->setConfig($config);
        $writer->write();
        return $this;
    }
}
