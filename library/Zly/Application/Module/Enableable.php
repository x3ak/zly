<?php
namespace Zly\Application\Module;

interface Enableable
{
    /**
     * Method for enable module
     */
    public function enable();
    
    /**
     * Method for disable module environment
     */
    public function disable();
}

