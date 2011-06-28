<?php
/**
 * Slys Doctrine 2 resource
 * @author evgheni.poleacov@gmail.com
 */
namespace Slys\Application\Resource;

class Doctrine2 extends \Zend\Application\Resource\AbstractResource
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
     * Resource initialization
     * @return Slys\Application\Resource\Doctrine2
     */
    public function init()
    {
        $front = $this->getBootstrap()->getBroker()->load('frontcontroller')->getFrontController();
        $connectionOptions = $this->getOptions();
        
        if (!empty($connectionOptions['cache'])) {
            $cacheClass = '\Doctrine\Common\Cache\\'.ucfirst($connectionOptions['cache']).'Cache';
            $cache = new $cacheClass();
        } else {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        }
        
        $boostrap = $this->getBootstrap()->getApplication();
        if($boostrap instanceof \Zend\Application\Application)
            $boostrap = $this->getBootstrap();
        
        $modules = $boostrap->getResource('modules');
        
        if(empty($modules))
            return false;
        
        foreach($modules as $name=>$module) {
                
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
        $config->setProxyDir('/slys/proxies');
        $config->setProxyNamespace('Slys\Proxies');


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
        $front->setParam('doctrine2', $this);

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