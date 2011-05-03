<?php
/**
 * Slys Doctrine 2 resource
 * @author evgheni.poleacov@gmail.com
 */
class Slys_Application_Resource_Doctrine2 extends Zend_Application_Resource_ResourceAbstract
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
     * @return Slys_Application_Resource_Doctrine2
     */
    public function init()
    {
        set_include_path(
                implode(
                        PATH_SEPARATOR, array(
                                realpath(ROOT_PATH . '/library/Doctrine/'),
                                get_include_path(),
                        )
                )
        );
        
        $this->getBootstrap()->getApplication()->setAutoloaderNamespaces(
            array('Doctrine','Symfony'));


        $front = $this->getBootstrap()
                      ->getPluginResource('frontController')
                      ->getFrontController();

        if (APPLICATION_ENV == "development") {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }

        foreach($front->getControllerDirectory() as $name=>$path) {
            $entityPath = dirname($path).'/models/mappers';
            if(is_dir($entityPath) && is_readable($entityPath)) {
                $this->_entitiesPaths[$name] = $entityPath;
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
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }

        $connectionOptions = $this->getOptions();

        if($connectionOptions === null)
            $connectionOptions = array('driver'=>'pdo_mysql');

        $this->_em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

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