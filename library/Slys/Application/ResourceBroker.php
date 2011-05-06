<?php

namespace Slys\Application;

class ResourceBroker extends \Zend\Application\ResourceBroker 
{
   protected $defaultClassLoader = 'Slys\Application\ResourceLoader';
}
