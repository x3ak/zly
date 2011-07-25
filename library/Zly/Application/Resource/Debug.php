<?php
/**
 * Zly
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://zendmania.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zendmania.com so we can send you a copy immediately.
 *
 * @category   Zly
 * @package    Zly
 * @copyright  Copyright (c) 2010-2011 Evgheni Poleacov (http://zendmania.com)
 * @license    http://zendmania.com/license/new-bsd New BSD License
 * @version    $Id: Zly.php 937 2010-12-30 15:01:37Z deeper $
 */
namespace Zly\Navigation\Resource;

class Debug extends \Zend\Application\Resource\ResourceAbstract
{

    public function init()
    {
        $options = $this->getOptions();
        if(!empty($options['enabled'])) {
            $this->getBootstrap()->bootstrap('frontcontroller');


            $front = $this->getBootstrap()->getResource('frontcontroller');
            $autoloader = \Zend\Loader\Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');

            $options = array(
                'plugins' => array('Variables',
                                   'File' => array('base_path' => APPLICATION_PATH),
                                   'Memory',
                                   'Time',
                                   'Registry',
                                   'Exception')
            );

            $debug = new \ZFDebug\Controller\Plugin\Debug($options);

            $this->getBootstrap()->bootstrap('frontController');
            $frontController = $this->getBootstrap()->getResource('frontcontroller');
            $frontController->registerPlugin($debug);


            if($front->hasPlugin('ZFDebug_Controller_Plugin_Debug')) {
                if(!$this->getBootstrap()->hasPluginResource('doctrine')) {
                    $this->getBootstrap()->registerPluginResource('doctrine');
                    $this->getBootstrap()->bootstrap('doctrine');
                }
                    $zfDebug = $front->getPlugin('ZFDebug_Controller_Plugin_Debug')
                        ->registerPlugin(new \Zly\Controller\Plugin\Debug\Doctrine());
            }
        }
    }
}
