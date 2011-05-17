<?php

/**
 * 
 */

namespace Slysman;

/**
 * 
 */
class ModulesController extends \Zend\Controller\Action
{
    protected $_requiredOptions = array(
        'name',
        'version',
        'description',
        'compatible'
    );
    
    /**
     * Action queue for post actions
     * @var \Zend\Queue 
     */
    protected $_queue;
    
    /**
     * 
     */
    public function init()
    {
        $this->modulesBootstraps = \Zend\Controller\Front::getInstance()
                ->getParam('bootstrap')->getResource('modules');
        
        foreach($this->modulesBootstraps as $key=>$bootstrap) {
            $bootstrap->isSlysCompatible = $this->_checkSlysCompatible($bootstrap);
            $bootstrap->moduleRequirements = $this->_checkModuleRequirements($bootstrap);
            $this->modulesBootstraps[$key] = $bootstrap;
        }
        $queueOptions = array(
            'name' => 'SlysPostAction',
        );
        $this->_queue = new \Zend\Queue\Queue('ArrayAdapter', $queueOptions);
    }

    /**
     *
     * @return type 
     */
    public function indexAction()
    {
        $modulesForm = new \Slysman\Form\Modules(); 
        
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
    
    /**
     * 
     */
    public function installAction()
    {
        $module = $this->getRequest()->getParam('item');

        $bootstrap = $this->modulesBootstraps[$module];
        if( $bootstrap instanceof \Slys\Application\Module\Installable) {
            $result = $bootstrap->install($this->_queue);

        }
        $this->view->result = $result;
    }
    
    /**
     * 
     */
    public function updateAction()
    {
        $stack = array();
        foreach($this->modulesBootstrap as $moduleName=>$bootstrap) {
            if($bootstrap instanceof \Slys\Application\Module\Updateable) {                
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
    
    /**
     * 
     */
    public function uninstallAction()
    {
        $stack = array();
        foreach($this->modulesBootstrap as $moduleName=>$bootstrap) {
            if($bootstrap instanceof \Slys\Application\Module\Installable) {
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
    
    /**
     *
     * @param \Zend\Application\Module\Bootstrap $bootstrap
     * @return boolean 
     */
    protected function _checkSlysCompatible(\Zend\Application\Module\Bootstrap $bootstrap)
    {   
        $compatible = true;
        foreach($this->_requiredOptions as $option) {
            if(!$bootstrap->hasOption($option)) {
                $compatible = false;
            }
        }
        return $compatible;
    }
    
    /**
     *
     * @param \Zend\Application\Module\Bootstrap $bootstrap
     * @return type 
     */
    protected function _checkModuleRequirements(\Zend\Application\Module\Bootstrap $bootstrap)
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

