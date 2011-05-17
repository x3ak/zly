<?php
namespace Slys\Application\Module;

interface Installable
{
    /**
     * Method for install module environment
     */
    public function install(\Zend\Queue\Queue $queue);
    
    /**
     * Method for uninstall module environment
     */
    public function uninstall(\Zend\Queue\Queue $queue);
}

