<?php
/**
 * Slys 2
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 * 
 */

namespace Sysmap;

use \Slys\Application\Module as Module, 
    \Slys\Api as Api;

class Bootstrap extends \Zend\Application\Module\Bootstrap implements Api\Request\Requestable
{
    public function onRequest(\Slys\Api\Request $request) 
    {

        $mapModel = new Model\Map();
        
        switch ($request->getName()) {
            case 'sysmap.get-map-tree':
                $request->getResponse()->setData( $mapModel->getMapTreeElement() );
                break;

            case 'sysmap.currently-active-items':
                $params = $request->getParams();

                if (empty($params['request']) === false and $params['request'] instanceof \Zend\Controller\Request\AbstractRequest)
                    $request->getResponse()->setData( $mapModel->getActiveItems($params['request']) );
                else
                    $request->getResponse()->setData( $mapModel->getActiveItems() );

                break;

            case 'sysmap.get-item-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( $mapModel->getItemByHash($params['identifier']) );

                break;

            case 'sysmap.get-item-parents-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $request->getResponse()->setData( $mapModel->getItemParentsByHash($params['identifier']) );

                break;
        }
    }
}