<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', './application');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH,
    APPLICATION_PATH.'/library',
    '/usr/local/lib/phpdaemon/lib',
    APPLICATION_PATH.'/library/Zend/library',
    get_include_path()
)));

class Zly extends AppInstance
{
    public $appFrontController = null;

    public function init()
    {
        Daemon::log(__CLASS__ . ' up');

        require_once 'Zend/Application/Application.php';
        $application = new Zend\Application\Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $bootstrap = $application->bootstrap()->getBootstrap();

        $this->appFrontController = $bootstrap->getResource('FrontController');
        $this->appFrontController->setParam('bootstrap', $bootstrap);
        $this->appFrontController->returnResponse(true);
    }

    public function onReady()    { /** after initialization */ }
    public function onShutdown() { return true; }

    public function beginRequest($request, $upstream)
    {
        return new ZfconfRequest($this, $upstream, $request);
    }

}

class ZfconfRequest extends HTTPRequest
{
    public $response = null;

    public function run()
    {
        $this->response = null;
        $this->appInstance->appFrontController->setRequest('\Zend\Controller\Request\Http');
        $this->appInstance->appFrontController->setResponse('\Zend\Controller\Response\Http');
        $this->appInstance->appFrontController->returnResponse(true);

        $this->response = $this->appInstance->appFrontController->dispatch();

        return Request::DONE;
    }

    public function onFinish()
    {
        Daemon::log(__CLASS__ . ' finished request processing for ' . $_SERVER['REQUEST_URI']);
        try {
            if (null === $this->response) { throw new Exception('NULL response provided'); }
            $this->response->renderExceptions(false);
            print $this->response->outputBody();
        } catch (Exception $e) {
            Daemon::log(__CLASS__ . ' Exception ' . $e->getMessage());
        }
    }
}

return new Zly();