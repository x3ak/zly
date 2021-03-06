<?php

namespace Zly\Application;

use Zend\Loader\PluginClassLoader;

class ResourceLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased bootstrap resources
     */
    protected $plugins = array(
        'cachemanager'    => 'Zend\Application\Resource\CacheManager',
        'db'              => 'Zend\Application\Resource\Db',
        'dojo'            => 'Zend\Application\Resource\Dojo',
        'frontcontroller' => 'Zend\Application\Resource\FrontController',
        'layout'          => 'Zend\Application\Resource\Layout',
        'locale'          => 'Zend\Application\Resource\Locale',
        'log'             => 'Zend\Application\Resource\Log',
        'mail'            => 'Zend\Application\Resource\Mail',
        'modules'         => 'Zly\Application\Resource\Modules',
        'multidb'         => 'Zend\Application\Resource\MultiDb',
        'navigation'      => 'Zend\Application\Resource\Navigation',
        'router'          => 'Zend\Application\Resource\Router',
        'session'         => 'Zend\Application\Resource\Session',
        'translate'       => 'Zend\Application\Resource\Translate',
        'view'            => 'Zend\Application\Resource\View',
        'doctrine'       => 'Zly\Application\Resource\Doctrine',
        'zly'            => 'Zly\Application\Resource\Zly'
    );
}
