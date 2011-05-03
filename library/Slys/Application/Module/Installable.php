<?php
/**
 * 
 */
interface Slys_Application_Module_Installable
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

