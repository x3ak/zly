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
            case 'sysmap.get-map-form-element':
                $request->getResponse()->setData( $mapModel->getMapTreeElement() );
                break;

            case 'sysmap.get-map':
                $request->getResponse()->setData( $mapModel->getSysmap() );
                break;
            
            case 'sysmap.get-root-identifier':
                $request->getResponse()->setData( new \Zend\Acl\Resource\GenericResource($mapModel->getRoot()->hash) );
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
                    $node = $mapModel->getNodeByHash($params['identifier']);
                    if(isset($node->_childrens))
                        unset($node->_childrens);
                    $request->getResponse()->setData( $node );

                break;

            case 'sysmap.get-item-parents-by-identifier':
                $params = $request->getParams();

                if (empty($params['identifier']) === false and is_string($params['identifier']) === true)
                    $parentsHashes = array();
                    $parentsNodes = $mapModel->getParentByHash($params['identifier'], true);
                   
                    foreach($parentsNodes as $node) {
                        $parentsHashes[] = new \Zend\Acl\Resource\GenericResource($node->hash);
                    }
                    $request->getResponse()->setData( $parentsHashes );

                break;
        }
    }
}