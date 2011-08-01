<?php
/**
 * Zly Doctrine 2 resource
 * @author evgheni.poleacov@gmail.com
 */
namespace Zly\Application\Resource;

class Doctrine extends \Zend\Application\Resource\AbstractResource
{
    
    /**
     * Entity Manager
     *
     * @var \Doctrine\EntityManager
     */
    protected $_em = null;

    /**
     * Paths to modules mappers
     * @var array
     */
    protected $_entitiesPaths = array();
    
    /**
     *
     * @var boolean 
     */
    protected $_started = false;
    
    /**
     *
     * @var \Zly\Application\Resource\Modules
     */
    protected $_modules;

    /**
     * Resource initialization
     * @return Zly\Application\Resource\Doctrine2
     */
    public function init()
    {       
        $connectionOptions = $this->getOptions();
        if (!empty($connectionOptions['cache'])) {
            $cacheClass = '\Doctrine\Common\Cache\\'.ucfirst($connectionOptions['cache']).'Cache';
            $cache = new $cacheClass();
        } else {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }
        
        $application = $this->getBootstrap();
        
        $this->_modules = $application->getBroker()->load('modules');

        if(empty($this->_modules))
            return false;
        
        foreach($this->_modules->getExecutedBootstraps() as $name=>$module) {

            if($module->getResourceLoader()->hasResourceType('mappers')) {
                $resourceTypes = $module->getResourceLoader()->getResourceTypes();
                $entityPath = $resourceTypes['mappers']['path'];

                if(is_dir($entityPath) && is_readable($entityPath)) {
                    $this->_entitiesPaths[$name] = $entityPath;
                }
            }

        }
        $config = new \Doctrine\ORM\Configuration;
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver($this->_entitiesPaths);
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $tempdir = sys_get_temp_dir();
        $proxydir = dirname(APPLICATION_PATH).'/data/cache/doctrine/proxies';
        if(!is_dir($proxydir)) {
            mkdir($proxydir, 0777, true);
        }
        $config->setProxyDir($proxydir);
        $config->setProxyNamespace('Doctrine\Proxy');


        if (APPLICATION_ENV == "development") {
//            echo '<pre>';
//            $logger = new \Doctrine\DBAL\Logging\EchoSQLLogger();
//            $config->setSQLLogger($logger);
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }

        

        if($connectionOptions === null)
            $connectionOptions = array('driver'=>'pdo_mysql');

        
        
        $this->_em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
        $evm = $this->_em->getEventManager();
        $evm = new \Doctrine\Common\EventManager();
        $treeListener = new \Zly\Doctrine\Tree\TreeListener();
        $evm->addEventSubscriber($treeListener);

        $front = $application->getBroker()->load('frontcontroller')->getFrontController();
        $front->setParam('doctrine', $this);
        return $this;
    }

    /**
     * Return Doctrine 2 entity manager
     * @return \Doctrine\EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }

    /**
     * Return entity configs paths
     * @return array
     */
    public function getEntityMetaPaths()
    {
        return $this->_entitiesPaths;
    }

}