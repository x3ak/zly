<?php

/**
 * Zly
 *
 * Template layout switcher. Used to check and switch layout for the current theme
 * such file exists
 *
 * @author Serghei Ilin <criolit@gmail.com>
 */
namespace Templater\Plugin;

class Layout extends \Zend\Controller\Plugin\AbstractPlugin
{

    /**
     * Templater options
     * @var array
     */
    protected $_options;
    /**
     * Layout object
     *
     * @var Zend_Layout
     */
    protected $_layout;
    
    /**
     * @var \Templater\Model\Layouts 
     */
    protected $_model;

    /**
     * Constructor
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
        
    }

    /**
     * Set templater options
     * @param array $options
     * @return Templater_Plugin_Layout
     */
    public function setOptions($options = array())
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Get Tempalter options
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    public function routeStartup()
    {
        $this->_model = new \Templater\Model\Layouts();
    }

    /**
     * On dispatch loop startup layout change is happens
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return void
     */
    public function dispatchLoopStartup(\Zend\Controller\Request\AbstractRequest $request)
    {        
        $config = new \Zend\Config\Config($this->getOptions());
        $themeSettings = $config->toArray();
        $apiRequest = new \Zly\Api\Request($this, 'sysmap.currently-active-items', array('request'=>$request));
        $mapIdentifiers = $apiRequest->proceed()->getResponse()->getFirst();

        /**
         * Get current layout from config
         */
        if(empty($mapIdentifiers)) {
            $currentLayout = $this->_model->getDefaultLayout();
        } else {
            $currentLayout = $this->_model->getCurrentLayout($mapIdentifiers);
        }

        
        $frontController = \Zend\Controller\Front::getInstance();        
              
        /**
         * Set current layout
         */
        if (!$frontController->hasPlugin('\Zend\Layout\Controller\Plugin\Layout')) {
            $layoutResource = $frontController->getParam('bootstrap')->getBroker()->load('layout');
            $layoutResource->init();            
        }
            
        $this->_layout = $frontController
            ->getPlugin('Zend\Layout\Controller\Plugin\Layout')
            ->getLayout();

        if (empty($currentLayout)) {
            $this->_layout->disableLayout();
            throw new \Zend\Layout\Exception('No active layouts for theme found or no active theme found');
        }

        $layoutPath = $config->directory . DIRECTORY_SEPARATOR .
                $currentLayout->getTheme()->getName() . DIRECTORY_SEPARATOR .
                $themeSettings['layout']['directory'];

        $layoutName = $currentLayout->getName();
        $layoutFile = realpath($layoutPath . DIRECTORY_SEPARATOR . $layoutName . '.phtml');
        
        if (file_exists($layoutFile)) {
            
            $this->_layout->setLayoutPath($layoutPath);
            $this->_layout->setLayout($layoutName);

            $frontController->setParam('noErrorHandler', true);
            $frontController->registerPlugin(new \Templater\Plugin\ErrorHandler(), 98);
            
            if(!$request->isXmlHttpRequest())
                $this->getHelperBroker()->register('Widget',
                    new \Templater\Library\Controller\Action\Helper\Widget($this->getOptions()));
        } else {
            throw new Zend_Exception('Layout "' . $layoutPath .
                    DIRECTORY_SEPARATOR . $layoutName . '" established for this page not found');
        }
    }

}