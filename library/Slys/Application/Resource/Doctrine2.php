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
//       $this->getBootstrap()->setAutoloaderNamespaces( array(
//            'Doctrine\ORM'=> APPLICATION_PATH.'/../library/Doctrine/Orm/lib/Doctrine/ORM',
//            'Doctrine\Common'=> APPLICATION_PATH.'/../library/Doctrine/Common/lib/Doctrine/Common',
//            'Doctrine\DBAL'=> APPLICATION_PATH.'/../library/Doctrine/Dbal/lib/Doctrine/DBAL',
//            'Symfony'
//        ));

        $front = $this->getBootstrap()->getBroker()->load('frontcontroller')->getFrontController();

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