<?php
namespace Zly\Application\Module;

interface Installable
{
    /**
     * Method for install module environment
     */
    public function install();
    
    /**
     * Method for uninstall module environment
     */
    public function uninstall();
}

