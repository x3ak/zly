<?php
/**
 * Slys
 *
 * Map module bootstrap class
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id: Bootstrap.php 1231 2011-04-17 17:49:48Z deeper $
 */


namespace Sysmap;

use \Slys\Application\Module as Module, 
    \Slys\Api as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap implements Api\Request\Requestable
{
    public function onRequest(\Slys\Api\Request $request) {
        switch ($request->getName()) {
            case 'sysmap.get-map-tree':
                $request->getResponse()->setData( \Sysmap\Model\Map::getInstance()->getMapTreeElement() );
                break;

            case 'sysmap.currently-active-items':
                $params = $request->getParams();
                
                if (empty($params['request']) === false and $params['request'] instanceof \Zend\Controller\Request\AbstractRequest)
                    $request->getResponse()->setData( Model\Map::getInstance()->getActiveItems($params['request']) );
                else
                    $request->getResponse()->setData( Model\Map::getInstance()->getActiveItems() );

                break;

            case 'sysmap.get-item-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( Model\Map::getInstance()->getItemByHash($params['identifier']) );

                break;

            case 'sysmap.get-item-parents-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( Model\Map::getInstance()->getItemParentsByHash($params['identifier']) );

                break;
        }
    }
}