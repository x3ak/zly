<?php
namespace Slysman;
class IndexController extends \Zend\Controller\Action
{
    protected $messages = array();
    protected $_directories = array(
        '/tmp',
        '/data/cache',
        '/data/sessions',
        '/public'
    );


    public function preDispatch()
    {
         $this->modulesBootstrap = \Zend\Controller\Front::getInstance()
                ->getParam('bootstrap')->getResource('modules');
    }
    
    public function indexAction()
    {
        
    }


    public function requirementsAction()
    {

        if($this->getRequest()->isPost()) {

            $finish = $this->getRequest()->getParam('finish');
            if(!empty($finish)) {                
                $this->_redirect($this->_helper->url('install','install'));
            }
        }
    
        $options = $this->getInvokeArg('bootstrap')->getApplication()->getOptions();
        
        $application = $this->getInvokeArg('bootstrap')->getApplication();
        
        if($application instanceof \Zend\Application) {
            $app = $application
                ->getBootstrap()
                ->getResourceLoader()
                ->getBasePath().
                DIRECTORY_SEPARATOR.'configs'.
                DIRECTORY_SEPARATOR.'application.ini';
        }
        
        $this->_checkDirectories();

        
    }

    protected function _checkDirectories()
    {
        foreach($this->_directories as $dir) {
            $path = APPLICATION_PATH . '/..'.$dir;
            if(!is_writable($path))
                $this->messages[] = new \Zend\Application\Exception\RuntimeException($dir.': Path not writeable.');
            else
                $this->messages[] = $dir.': Path writeable';
        }
    }




    public function postDispatch()
    {
        $this->view->messages = $this->messages;
    }


}

