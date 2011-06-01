<?php
/**
 * PHPUnit tests for sysmap bootstrap
 *
 * @author evgheni.poleacov@gmail.com
 */
namespace Sysmap;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_application = \Zend\Controller\Front::getInstance()->getParam('bootstrap')->getApplication();
    }

    /**
     * Test requestable feature
     * @group sysmap
     */
    public function testRequestable()
    {
        $sysmapBootstrap = new \Sysmap\Bootstrap($this->_application);
        
        $requests = array(
            new \Slys\Api\Request($this, 'sysmap.currently-active-items'),
//            new \Slys\Api\Request($this, 'sysmap.get-map-tree'),
//            new \Slys\Api\Request($this, 'sysmap.get-item-by-identifier'),
//            new \Slys\Api\Request($this, 'sysmap.get-item-parents-by-identifier'),
        );
        foreach($requests as $request) {
            $result = $sysmapBootstrap->onRequest($request);
            switch($request->getName()) {
                case 'sysmap.currently-active-items': 
                    $this->assertFalse(is_array($result));
                break;
            }
        }
    }    
}
