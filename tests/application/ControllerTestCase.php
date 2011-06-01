<?php
/**
 * Slys
 *
 * Base unit test class for ZF
 *
 * @author     Serghei Ilin <criolit@gmail.com>
 * @version    $Id: Main.php 461 2010-10-22 15:09:14Z criolit $
 */
abstract class ControllerTestCase extends \Zend\Test\PHPUnit\ControllerTestCase
{
	protected $_application;

	protected function setUp()
	{
		$this->bootstrap = array($this, 'appBootstrap');
		parent::setUp();
	}

	public function appBootstrap()
	{
		$this->_application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH. '/configs/application.ini');
		$this->_application->bootstrap();

	/**
         * Fix for ZF-8193
         * http://framework.zend.com/issues/browse/ZF-8193
         * Zend_Controller_Action->getInvokeArg('bootstrap') doesn't work
         * under the unit testing environment.
         */
        $front = Zend_Controller_Front::getInstance();
        if($front->getParam('bootstrap') === null)
            $front->setParam('bootstrap', $this->_application->getBootstrap());
	}
}