<?php

namespace Zly\Application;

class ResourceBroker extends \Zend\Application\ResourceBroker 
{
   protected $defaultClassLoader = 'Zly\Application\ResourceLoader';
}
