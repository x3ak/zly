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
    public function proceedAction()
    {
        $module = $this->getRequest()->getParam('item');
        $deed = $this->getRequest()->getParam('deed');

        $bootstrap = $this->modulesBootstraps[$module];
        if( $bootstrap instanceof \Slys\Application\Module\Installable) {
            $result = $bootstrap->{$deed}();
            if($result instanceof \Zend\Controller\Request\AbstractRequest) {
                $this->_forward(
                        $result->getActionName(), 
                        $result->getControllerName(), 
                        $result->getModuleName(), 
                        $result->getParams()
                );
            } else {
                $this->_redirect($this->broker('url')->direct('index','modules','slysman'));
                return true;
            }

        }
        
        $this->view->result = $result;
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

