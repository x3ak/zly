<?php
/**
 * @version    $Id: MenuItem.php 1136 2011-01-28 15:50:56Z criolit $
 */
namespace Navigation\Form;

use Zend\Form\Element as Element;

class MenuItem extends \Zend\Form\Form
{
    /**
     *
     * @var \Navigation\Model\Navigation 
     */
    protected $model;
    
    public function init()
    {
        if(!$this->model instanceof \Navigation\Model\Navigation)
            throw new \Exception('Form model not provided, model instance of \Navigation\Model\Navigation');
        
        $this->setMethod('post')
                 ->setAction(
                        $this->getView()->broker('url')->direct(array('module' => 'navigation', 'controller' => 'admin', 'action' => 'edit-menu-item'))
                 );

        $menuItemTitle = new Element\Text('title');
        $menuItemTitle->setLabel('Title')->setRequired(true);
        $this->addElement($menuItemTitle);

        $navigator = new \Zly\Form\Element\Tree('parent_id');
        $navigator->setMultiple(false)
                  ->setRequired(true)
                  ->setValueKey('id')
                  ->setTitleKey('label')
                  ->setChildrensKey('pages');
        $navigation = $this->model->getNavigation();

        if(!empty($navigation))
            $navigator->setMultiOptions( $navigation->toArray() );

        $this->addElement($navigator);

        $menuItemType = new Element\Select('type');
        $menuItemType->setLabel('Menu item type')
             ->setAllowEmpty(false)
             ->setRequired(true)
             ->addMultiOptions(array(
                    '' => '',
                    \Navigation\Model\Navigation::TYPE_EXTERNAL => 'External',
                    \Navigation\Model\Navigation::TYPE_PROGRAMMATIC => 'Programmatic'
             ));
        $this->addElement($menuItemType);

        $submit = new Element\Submit('menu_item_submit');
        $submit->setIgnore(true)
                   ->setLabel('Save')
                   ->setOrder(100)
                   ->setIgnore(true);
        $this->addElement($submit);

        $this->addElement('hidden', 'id');
    }
    
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Populate form
     *
     * Proxies to {@link setDefaults()}
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate(array $data)
    {
        $this->_appendSubForm($data['type']);

        $data['options']['external_link'] = $data['external_link'];
        $data['options']['sysmap_identifier'] = $data['sysmap_identifier'];

        if ($data['type'] == \Navigation\Model\Navigation::TYPE_PROGRAMMATIC and !empty($data['options']['sysmap_identifier']))
            $this->_prepareRoutes($data['options']['sysmap_identifier']);

        return parent::populate($data);
    }

    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {
        $this->_appendSubForm($data['type']);

        if ($data['type'] == \Navigation\Model\Navigation::TYPE_PROGRAMMATIC and !empty($data['options']['sysmap_identifier']))
            $this->_prepareRoutes($data['options']['sysmap_identifier']);

        return parent::isValid($data);
    }

    protected function _appendSubForm($type)
    {
        if (empty($type) === false) {
            if ($type == \Navigation\Model\Navigation::TYPE_EXTERNAL)
                $this->addSubForm(new \Navigation\Form\ExternalType(), 'options', $this->getElement('menu_item_submit')->getOrder() - 2);
            elseif($type == \Navigation\Model\Navigation::TYPE_PROGRAMMATIC)
                $this->addSubForm(new \Navigation\Form\ProgrammaticType(), 'options', $this->getElement('menu_item_submit')->getOrder() - 2);
        }
    }

    protected function _prepareRoutes($sysmapId)
    {
        $routes = array('' => '');

        if (!empty($sysmapId)) {
            /** @var $router Zend_Controller_Router_Rewrite */
            $router = Zend_Controller_Front::getInstance()->getRouter();

            // check if current router supports getRoutes method
            if (in_array('getRoutes', get_class_methods(get_class($router)))) {
                $tmpRoutes = $router->getRoutes();

                /** @var $mcaMapper Sysmap_Model_Mapper_Sysmap */
                $mcaMapper = Sysmap_Model_DbTable_Sysmap::getInstance()->findOneBy('hash', $sysmapId);
                $request = $mcaMapper->toRequest(true);

                foreach($tmpRoutes as $routeName => $route) {
                    $requestArray = array('module' => $request->getModuleName(), 'controller' => $request->getControllerName(), 'action' => $request->getActionName());
                    $requestArray += $request->getParams();

                    /** @var $pageRoute Zend_Controller_Router_Route_Regex */
                    $pageRoute = $router->getRoute($routeName);
                    try {
                        $ret = $pageRoute->assemble($requestArray);
                    }
                    catch(Exception $ex) {
                        continue;
                    }

                    $result = $pageRoute->match($ret);
                    $result = array_diff_assoc($requestArray, $result);
                    if (!empty($result))
                        continue;

                    $routes[$routeName] = $routeName . ' - /' . $ret;
                }
            }

            $routeElem = new Zend_Form_Element_Select('route');
            $routeElem->setLabel('url_route')
                      ->setMultiOptions($routes)
                      ->setRequired(true)
                      ->setAllowEmpty(false)
                      ->setOrder($this->getElement('menu_item_submit')->getOrder() - 1);
            $this->addElement($routeElem);
        }
    }
}