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
 * @version    $Id: Zly.php 1193 2011-02-17 10:43:39Z criolit $
 */
namespace Zly\Application\Resource;

class Zly extends \Zend\Application\Resource\AbstractResource
{


    /**
     * Zly requirements initialization
     */
    public function init()
    {
        $this->getBootstrap()->getBroker()->load('view');
        if(empty($this->getBootstrap()->view))
            $view = $this->getBootstrap()->bootstrap('view');        
        $view = $this->getBootstrap()->getBroker()->load('view')->getView();        
        $view->broker()->setClassLoader(new \Zly\View\HelperLoader());
        return $this;
    }
}
