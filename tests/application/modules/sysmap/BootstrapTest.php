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
            new \Slys\Api\Request($this, 'sysmap.get-map'),
            new \Slys\Api\Request($this, 'sysmap.get-map-form-element'),
            new \Slys\Api\Request($this, 'sysmap.get-item-by-identifier', 
                    array('identifier'=> 'def8134b3963fae594dc7b54adfd367d')),
            new \Slys\Api\Request($this, 'sysmap.get-item-parents-by-identifier', 
                    array('identifier'=> 'def8134b3963fae594dc7b54adfd367d')),
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
                    
                    case 'sysmap.get-map':

                        $this->assertTrue(is_array($response) , 'sysmap map can\'t be empty'); 
                        $this->assertFalse(empty($response) , 'sysmap should return active items always');
                        /**
                         * Check map basic structure to level 3
                         */
                        foreach($response as $module) {
                            foreach($module->_childrens as $controller) {
                                foreach($controller->_childrens as $action) {
                                    $this->assertTrue(!empty($action->hash) && is_string($action->hash) , 'sysmap action hash should be not empty and as string type'); 
                                    $this->assertTrue($action->level == 3 , 'sysmap action level always equal to 3'); 
                                }
                            }
                        }
                    break;
                    
                    case 'sysmap.get-map-form-element':
                        $this->assertTrue($response instanceof \Slys\Form\Element\Tree, 'sysmap form elements should as \Slys\Form\Element\Tree type '); 
                        $options = $response->getMultiOptions();
                        $this->assertTrue(!empty($options), 'Sysmap tree element should be always have options');
                    break;
                
                    case 'sysmap.get-item-by-identifier':
                        $this->assertTrue((!empty($response) && is_object($response)) || $response === false, 'requested sysmap node should be object or false if not found '); 
                    break;
                
                    case 'sysmap.get-item-parents-by-identifier':
                        $this->assertTrue((!empty($response) && is_array($response)) || $response === false, 'requested sysmap node should be object or false if not found '); 
                    break;
                }
            }

        }
    }    
}
