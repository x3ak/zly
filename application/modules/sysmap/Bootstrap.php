<?php
/**
 * Slys
 *
 * Map module bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1231 2011-04-17 17:49:48Z deeper $
 */
class Sysmap_Bootstrap extends Zend_Application_Module_Bootstrap implements Slys_Api_Request_Requestable
{
    public function onRequest(Slys_Api_Request $request) {
        switch ($request->getName()) {
            case 'sysmap.get-map-tree':
                $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getMapTreeElement() );
                break;

            case 'sysmap.currently-active-items':
                $params = $request->getParams();
                
                if (empty($params['request']) === false and $params['request'] instanceof Zend_Controller_Request_Abstract)
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getActiveItems($params['request']) );
                else
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getActiveItems() );

                break;

            case 'sysmap.get-item-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getItemByHash($params['identifier']) );

                break;

            case 'sysmap.get-item-parents-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( Sysmap_Model_Map::getInstance()->getItemParentsByHash($params['identifier']) );

                break;
        }
    }
}