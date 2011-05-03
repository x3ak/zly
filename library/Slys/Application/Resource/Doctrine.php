<?php

class Slys_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{
    public $_explicitType = 'doctrine';

    public function  __construct($options = null)
    {
        parent::__construct($options);
    }

    public function init()
    {
        $config = new Zend_Config($this->getOptions());

        if ($config->count() < 1)
            return false;

        set_include_path( implode( PATH_SEPARATOR, array(
            realpath(ROOT_PATH . '/library/Doctrine1'),
            get_include_path(), ) ) );
        
        $this->getBootstrap()->getApplication()->getAutoloader()
             ->registerNamespace('Doctrine_');

        $doctrineManager = Doctrine_Manager::getInstance();
        $doctrineManager->registerConnectionDriver('mysql', 'Slys_Doctrine_Connection_Mysql');
        $doctrineManager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        $doctrineConnection = Doctrine_Manager::connection($config->dsn, 'doctrine');

        $doctrineManager->getCurrentConnection()->setCollate('utf8_unicode_ci');
        $doctrineManager->getCurrentConnection()->setCharset('utf8');
        $doctrineManager->getCurrentConnection()->setAttribute(Doctrine_Core::ATTR_IDXNAME_FORMAT,'%s');

        $doctrineConnection->prepare('SET NAMES UTF8')->execute();

        return $doctrineManager;
    }
}