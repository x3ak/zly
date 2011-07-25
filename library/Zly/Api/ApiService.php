<?php
/**
 * Zly Api manager class
 * @version $Id: Api.php 1231 2011-04-17 17:49:48Z deeper $
 */

namespace Zly\Api;

class ApiService
{
    /**
     * @var Zly_Api
     */
    protected static $_instance = null;
    /**
     * @var array
     */
    protected $_notifications = array();
    /**
     * @var array
     */
    protected $_processedRequests = array();
    /**
     * @var array
     */
    protected $_processedNotifications = array();

    /**
     * @var Zly_Api_Notification_Registry
     */
    protected $_notificationRegistry = null;

    /**
     * Constructor is closed from public visibility for singleton pattern
     */
    protected function __construct()
    {
        $this->_notificationRegistry = new \Zly\Api\Notification\Registry();
        $this->_collectNotifications();
    }

    /**
     * Returns the Zly_Api object
     * @static
     * @return Zly_Api
     */
    public static function getInstance()
    {
        if (self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    public function isRequestInProcess($requestName)
    {
        return in_array($requestName, $this->_processedRequests);
    }

    public function isNotificationInProgress($notificationName)
    {
        return in_array($notificationName, $this->_processedNotifications);
    }

    /**
     * @param Zly_Api_Request $request
     * @return Zly_Api_Request_Response
     */
    public function request(\Zly\Api\Request $request)
    {
//        if (in_array($request->getName(), $this->_processedRequests) === true) {
//            user_error('Possible request loop! Request \'' . $request->getName() . '\' was already processed!', E_USER_WARNING);
//            return;
//        }

        $this->_processedRequests[] = $request->getName();

        $response = array();
        $resultResponse = array();
        $responcePriorities = array();

        /** @var $bootstraps ArrayObject */
        $bootstraps = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getResource('modules');

        foreach($bootstraps as $key=>$bootstrap) {
            if ($bootstrap instanceof \Zly\Api\Request\Requestable) {
                $bootstrap->onRequest($request);

                if ($request->getResponse()->getData() !== null) {
                    $resp = $request->getResponse()->getData();

                    if (empty($resp) === false) {
                        $response[] = $resp['data'];
                        $responcePriorities[] = $resp['priority'];
                    }
                }
            }  
        }

        if (empty($response) === true) {
            user_error('The response for the request \'' . $request->getName() . '\' is empty! Request is not processed!', E_USER_WARNING);
            return;
        }

        asort($responcePriorities, SORT_NUMERIC);
        $responcePriorities = array_reverse($responcePriorities, true);

        foreach($responcePriorities as $key => $dummy)
            $resultResponse[] = $response[$key];

        $this->_processedRequests = array();

        $request->getResponse()->setData($resultResponse);

        return $request->getResponse();
    }

    /**
     * @param  $context
     * @param  Zly_Api_Notification $notification
     * @return void
     */
    public function notify($context, $name, $params = array())
    {
        $notification = new \Zly\Api\Notification($context, $name, $params);

        if (in_array($notification->getName(), $this->_processedNotifications)) {
            user_error('Possible notification loop! Notification \'' . $notification->getName() . '\' was already processed!', E_USER_WARNING);
            return;
        }

        if ( $this->_notificationRegistry->isRegistered( $notification->getName() ) === false) {
            user_error('Use of unregistered notification name \'' . $notification->getName() . '\'! Notification is not processed', E_USER_WARNING);
            return;
        }

        $this->_processedNotifications[] = $notification->getName();

        $bootstraps = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getResource('modules');

        foreach($bootstraps as $bootstrap) {
            if ($bootstrap instanceof Zly_Api_Notification_Notifiable) {
                $bootstrap->onNotification($notification);
            }
        }

        $this->_processedNotifications = array();
    }

    protected function _collectNotifications()
    {
        $bootstraps = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getResource('modules');

        foreach($bootstraps as $bootstrap) {
            if ($bootstrap instanceof \Zly\Api\Notification\Notifier) {
                $bootstrap->publishNotifications( $this->_notificationRegistry );
            }
        }

    }
}