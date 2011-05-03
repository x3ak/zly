<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {

    }

    public function genMigrationAction()
    {
        foreach(Zend_Controller_Front::getInstance()->getControllerDirectory() as $name=>$dir) {
            $mapperPath = realpath($dir.'/../models/mappers');
            if($mapperPath)
                $modelsdirs[] = $mapperPath;
        }
        Zend_Debug::dump($modelsdirs);
        Doctrine_Core::generateMigrationsFromModels(APPLICATION_PATH.'/../tmp/migrations', $modelsdirs);
    }
}