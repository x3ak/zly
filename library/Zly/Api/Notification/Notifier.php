<?php
/**
 * Created by JetBrains PhpStorm.
 * User: criollit
 * Date: 17.01.11
 * Time: 18:01
 * To change this template use File | Settings | File Templates.
 */

interface Zly_Api_Notification_Notifier
{
    /**
     * @abstract
     * @param Zly_Api_Notification_Registry $registry
     * @return void
     */
    public function publishNotifications( Zly_Api_Notification_Registry $registry );
}