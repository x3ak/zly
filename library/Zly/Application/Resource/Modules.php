<?php

/**
 * Zly
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://zendmania.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zendmania.com so we can send you a copy immediately.
 *
 * @category   Zly
 * @package    Zly
 * @copyright  Copyright (c) 2010-2011 Evgheni Poleacov (http://zendmania.com)
 * @license    http://zendmania.com/license/new-bsd New BSD License
 * @version    $Id: Modules.php 1249 2011-04-28 15:02:58Z deeper $
 */
namespace Zly\Application\Resource;

class Modules extends \Zend\Application\Resource\Modules
{    

    /**
     * Initialize modules
     *
     * @return array
     * @throws Zend_Application_Resource_Exception When bootstrap class was not found
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('frontcontroller');
        $front = $bootstrap->getResource('frontcontroller');
        $modules = $front->getControllerDirectory();

        $default = $front->getDefaultModule();
        $curBootstrapClass = get_class($bootstrap);
        foreach ($modules as $module => $moduleDirectory) {
            $bootstrapClass = $this->_formatModuleName($module) . '\Bootstrap';
            if (!class_exists($bootstrapClass, false)) {
                $bootstrapPath  = dirname($moduleDirectory) . '/Bootstrap.php';
                if (file_exists($bootstrapPath)) {
                    $eMsgTpl = 'Bootstrap file found for module "%s" but bootstrap class "%s" not found';
                    include_once $bootstrapPath;
                    if (($default != $module)
                        && !class_exists($bootstrapClass, false)
                    ) {
                        throw new Exception\InitializationException(sprintf(
                            $eMsgTpl, $module, $bootstrapClass
                        ));
                    } elseif ($default == $module) {
                        if (!class_exists($bootstrapClass, false)) {
                            $bootstrapClass = 'Bootstrap';
                            if (!class_exists($bootstrapClass, false)) {
                                throw new Exception\InitializationException(sprintf(
                                    $eMsgTpl, $module, $bootstrapClass
                                ));
                            }
                        }
                    }
                } else {
                    continue;
                }
            }

            if ($bootstrapClass == $curBootstrapClass || $default == $module) {
                // If the found bootstrap class matches the one calling this
                // resource, don't re-execute.
                continue;
            }     
            
            // Custom modules options            
            $moduleConfig = $this->loadModuleConfig($moduleDirectory);
            if($bootstrap->getOption($module)) {
                $moduleConfig = $this->mergeOptions($moduleConfig, $bootstrap->getOptions());
            }
            if(!empty($moduleConfig))
            $bootstrap->setOptions($moduleConfig); 
            
            $moduleBootstrap = new $bootstrapClass($bootstrap); 
            // Zly custom module autoloader resources
            if($moduleBootstrap instanceof \Zend\Application\Module\Bootstrap)
                $moduleBootstrap->getResourceLoader()
                                ->addResourceTypes(array( 
                                    'library' => array( 'namespace' => 'Library', 'path' => 'library' ),
                                    'config' => array( 'namespace' => 'Config', 'path' => 'configs' ) 
                                 ));   
            
                     
            if($this->checkForLoadingBootstrap($moduleBootstrap))
            $moduleBootstrap->bootstrap();
            $this->_bootstraps[$module] = $moduleBootstrap;
        }
        return $this->_bootstraps;
    }

    public function enableModule($moduleName, $enabled = true)
    {
        $options['enabled'] = $enabled;
        return $this->setModuleOptions($moduleName, $options);
        return $this;
    }
    
    public function installModule($moduleName, $installed = true)
    {
        $options['installed'] = $installed;
        return $this->setModuleOptions($moduleName, $options);
    }
    
    public function setModuleOptions($moduleName, $options)
    {
        $configFile = $this->getBootstrap()->getApplication()->getApplication()->getOption('config');
        if(!empty($configFile)) {
            if(is_file($configFile) && is_readable($configFile) && is_writable($configFile)) {
                
                $sections = array();
                
                $configOptions = array( 'allowModifications' => true );
                $config = new \Zend\Config\Ini($configFile, null, $configOptions);
                foreach($config as $section=>$value) {
                    $config->merge(new \Zend\Config\Config(array($section=>array($moduleName=>$options))));
                }

            } else {
                $config = new \Zend\Config\Config(array('production'=>array($moduleName=>$options)));
            }
            $writer = new \Zend\Config\Writer\Ini();
            $writer->setConfig($config);
            $writer->setFilename($configFile);
            $writer->write();
        } else {
            throw new \Exception("For save modules local config required local config file and 'config'\n"
                    ." option with path ot in in main application config");
        }
        return $this;
    }
    
    public function loadModuleConfig($moduleDirectory)
    {
        $moduleConfigFile = realpath($moduleDirectory . '/../configs/module.ini');

        if ($moduleConfigFile) {
            $moduleConfig = new \Zend\Config\Ini($moduleConfigFile);
            $moduleConfig = $moduleConfig->get(APPLICATION_ENV);

            return $moduleConfig->toArray();
        } 
        else 
            return array();
        
    }
    
    protected function checkForLoadingBootstrap($bootstrap)
    {
        $bootstrapIt = true;

        if($bootstrap instanceof \Zly\Application\Module\Installable 
                && $bootstrap->hasOption('installed')) {

            $installed = $bootstrap->getOption('installed');
            if(empty($installed))
                $bootstrapIt = false;
        }

        if($bootstrap instanceof \Zly\Application\Module\Enableable 
                && $bootstrap->hasOption('enabled')) {
            $enabled = $bootstrap->getOption('enabled');
            if(empty($enabled))
                $bootstrapIt = false;
        } 

        return $bootstrapIt;
    }
    
}
