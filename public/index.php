<?php
/**
 * Slys index file
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname( dirname(__FILE__) ) );
defined('APPLICATION_PATH') or define('APPLICATION_PATH', ROOT_PATH . '/application');
defined('APPLICATION_ENV') or define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path( implode( PATH_SEPARATOR, array( realpath(ROOT_PATH . '/library'), realpath(ROOT_PATH . '/library/Zend/library'), get_include_path(), ) ) );

require_once 'Zend/Application/Application.php';

$application = new \Zend\Application\Application( APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini' );

$application->bootstrap()->run();

