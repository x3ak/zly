<?php
/**
 * Slys
 *
 * Theme support for the Slys applications
 *
 * @author Evgheni Poleacov <evgheni.poleacov@gmail.com>
 */

class Templater_Bootstrap extends Zend_Application_Module_Bootstrap 
    implements Slys_Api_Request_Requestable, 
               Slys_Application_Module_Installable,  
               Slys_Application_Module_Updateable
{
    
    public function initThemes($application)
    {
        
        $theme = Templater_Model_DbTable_Theme::getInstance()->getCurrentTheme();
        
        /**
         * Loading theme config
         */
        $navigationPath = $this->getOption('directory').
                DIRECTORY_SEPARATOR.$theme->name.
                DIRECTORY_SEPARATOR.'theme.ini';

        $themeConfig = new Zend_Config_Ini($navigationPath, APPLICATION_ENV);   
  
        $options = $themeConfig->toArray();

        if(!empty($options)) {
            $this->getApplication()->setOptions(
                    $this->getApplication()->mergeOptions(
                            $this->getApplication()->getOptions(), 
                            $options
                    ));
        }
        
    }

    /**
     * Tempalter plugins initialization
     */
    protected function _initPlugins()
    {
        $plugin = new Templater_Plugin_Layout($this->getOptions());
        
        Zend_Controller_Front::getInstance()
            ->registerPlugin($plugin);
    }

    public function onRequest(Slys_Api_Request $request)
    {
        switch ($request->getName()) {
            case 'navigation.get-module-navigation':
                $types = $this->getResourceLoader()->getResourceTypes();
                $navigationPath = $types['config']['path'].DIRECTORY_SEPARATOR.'navigation.yml';
                if(is_file($navigationPath)) {
                    $navigation = new Zend_Navigation_Page_Mvc(new Zend_Config_Yaml($navigationPath));
                    $request->getResponse()->setData($navigation);
                }
            break;
        }
    }
    
    public function install()
    {
        return 'Templater installed';
    }
    
    public function update()
    {
        return 'Templater updated';
    }
    
    public function uninstall()
    {
        return 'Templater uninstalled';
    }

}