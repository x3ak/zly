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
        
        $requests = array(
            new \Slys\Api\Request($this, 'sysmap.currently-active-items', 
                    array('request'=> new \Zend\Controller\Request\Simple('index', 'admin', 'sysmap'))),
//            new \Slys\Api\Request($this, 'sysmap.get-map-tree'),
//            new \Slys\Api\Request($this, 'sysmap.get-item-by-identifier'),
//            new \Slys\Api\Request($this, 'sysmap.get-item-parents-by-identifier'),
        );
        foreach($requests as $request) {

            $responses = $request->proceed()->getResponse();

            foreach($responses->getData() as $response) {
                switch($request->getName()) {

                    case 'sysmap.currently-active-items':
                        $this->assertTrue(is_array($response) , 'sysmap should return active items as array'); 
                        $this->assertFalse(empty($response) , 'sysmap should return active items always');
                        $this->assertFalse(count($response) < 4 , 'Minimal amount of active items is 4, returned:'.count($response));
                        foreach($response as $item) {
                            $this->assertTrue($item instanceof \Zend\Acl\Resource\GenericResource, 
                                    'every sysmap item should be instance of \Zend\Acl\Resource\GenericResource');
                        }
                    break;
                }
            }

        }
    }    
}
