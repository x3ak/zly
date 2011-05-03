<?php

class Slysman_ModulesController extends Zend_Controller_Action
{
    protected $_requiredOptions = array(
        'name',
        'version',
        'description',
        'compatible'
    );
    
    public function init()
    {
        $this->modulesBootstraps = Zend_Controller_Front::getInstance()
                ->getParam('bootstrap')->getResource('modules');
        
        foreach($this->modulesBootstraps as $key=>$bootstrap) {
            $bootstrap->isSlysCompatible = $this->_checkSlysCompatible($bootstrap);
            $bootstrap->moduleRequirements = $this->_checkModuleRequirements($bootstrap);
            $this->modulesBootstraps[$key] = $bootstrap;
        }
    }

    public function indexAction()
    {
        $modulesForm = new Slysman_Form_Modules(); 
        
        if($this->getRequest()->isPost() && $modulesForm->isValid($this->getRequest()->getPost())) {
            switch($modulesForm->getElement('action')->getValue()) {
                case 'install': 
                    $this->_forward('install');
                    return true;
                    break;
            }
        }

        $this->view->modulesForm = $modulesForm;
        $this->view->modules = $this->modulesBootstraps;
    }
    
    public function installAction()
    {
        $module = $this->getRequest()->getParam('item');

        $bootstrap = $this->modulesBootstraps[$module];
        if( $bootstrap instanceof Slys_Application_Module_Installable) {
            $result = $bootstrap->install();

        }
        $this->view->result = $result;
    }
    
    public function updateAction()
    {
        $stack = array();
        foreach($this->modulesBootstrap as $moduleName=>$bootstrap) {
            if($bootstrap instanceof Slys_Application_Module_Updateable) {                
                $stack[$moduleName]['bootstrap'] = $bootstrap;
                if($this->getRequest()->isPost()) {
                    $modules = $this->getRequest()->getPost('modules');
                    if(in_array($moduleName, $modules))
                        $stack[$moduleName]['result'] = $bootstrap->update();
                }
            }
        }
        $this->view->stack = $stack;
    }
    
    public function uninstallAction()
    {
        $stack = array();
        foreach($this->modulesBootstrap as $moduleName=>$bootstrap) {
            if($bootstrap instanceof Slys_Application_Module_Installable) {
                $stack[$moduleName]['bootstrap'] = $bootstrap;
                if($this->getRequest()->isPost()) {
                    $modules = $this->getRequest()->getPost('modules');
                    if(in_array($moduleName, $modules))
                        $stack[$moduleName]['result'] = $bootstrap->uninstall();
                }
            }
        }
        $this->view->stack = $stack;
    }
    
    protected function _checkSlysCompatible(Zend_Application_Module_Bootstrap $bootstrap)
    {   
        $compatible = true;
        foreach($this->_requiredOptions as $option) {
            if(!$bootstrap->hasOption($option)) {
                $compatible = false;
            }
        }
        return $compatible;
    }
    
    protected function _checkModuleRequirements(Zend_Application_Module_Bootstrap $bootstrap)
    {
        $requirements = array('modules'=>array(),'resources'=>array());
        if($bootstrap->hasOption('requires')) {
            $options = $bootstrap->getOption('requires');
            if(!empty($options['modules'])) {
                $requirements['modules'] = $options['modules'];
            }
            
            if(!empty($options['resources'])) {
                $requirements['resources'] = $options['resources'];
            }
        }

        return $requirements;
    }
}

