<?php
/**
 * Slys
 *
 * Unit testing bootstrap for PHPUnit and ZF
 *
 * @version    $Id: phpunit_bootstrap.php 461 2010-10-22 15:09:14Z criolit $
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname( dirname(__FILE__) ) );
defined('APPLICATION_PATH') or define('APPLICATION_PATH', ROOT_PATH . '/application');
defined('APPLICATION_ENV') or define('APPLICATION_ENV', 'testing');

set_include_path(
	implode(
		PATH_SEPARATOR, array(
			realpath(ROOT_PATH . '/library'),
			get_include_path(),
		)
	)
);

require_once 'Zend/Loader/Autoloader.php';
$autoLoader = Zend_Loader_Autoloader::getInstance();

require_once 'application/ControllerTestCase.php';