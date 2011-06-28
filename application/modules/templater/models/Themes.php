<?php

/**
 * Slys
 *
 * @version    $Id: Themes.php 1134 2011-01-28 14:31:15Z deeper $
 */
namespace Templater\Model;

class Themes extends \Slys\Doctrine\Model
{

    /**
     * Return collection of all tempaltes
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return $this->getEntityManager()
                    ->getRepository('Templater\Model\Mapper\Theme')
                    ->findAll();
    }

    /**
     * Return paginator for themes list
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @return \Zend\Paginator\Paginator 
     */
    public function getThemesPaginator($pageNumber = 1, $itemCountPerPage = 20)
    {
        $repo = $this->getEntityManager()->getRepository('Templater\Model\Mapper\Theme');
        $paginator = new \Zend\Paginator\Paginator($repo->getPaginatorAdapter());
        $paginator->setCurrentPageNumber($pageNumber)->setItemCountPerPage($itemCountPerPage);
        return $paginator;
    }

    /**
     * Return single theme
     * @param int $id
     * @param boolean $forUpdate
     * @return Templater_Model_Mapper_Theme
     */
    public function getTheme($id = null, $forUpdate = false)
    {
        if (!empty($id))
            $theme = $this->getEntityManager()
                          ->getRepository('\Templater\Model\Mapper\Theme')
                          ->findOneBy(array('id'=>$id));
        else
            $theme = false;

        if (empty($theme) && $forUpdate)
            $theme = new Mapper\Theme();

        return $theme;
    }

    /**
     * Disable all active tempaltes
     * @return boolean
     */
    public function disableAllThemes()
    {
        $activeThemes = Templater_Model_DbTable_Theme::getInstance()
                ->findByDql('current = ?', array(true));

        foreach ($activeThemes as $theme) {
            $theme->current = false;
            $theme->save();
        }
        return true;
    }

    /**
     * Return filled edit form for theme
     *
     * @param Templater_Model_Mapper_Theme $theme
     * @return Templater_Form_Theme
     */
    public function getThemeEditForm(Mapper\Theme $theme)
    {
        $themesDirs = $this->getThemesDirectoriesFromFS();

        $form = new \Templater\Form\Theme();
        if (empty($theme->id))
            $form->getElement('import_layouts')->setValue(true);

        $form->getElement('name')->addMultiOptions($themesDirs);
        $form->populate($theme->toArray());
        return $form;
    }

    /**
     * Save theme object into DB
     * @param Templater_Model_Mapper_Theme $theme
     * @param array $values
     * @return boolean
     */
    public function saveTheme(Mapper\Theme $theme, array $values)
    {
        $theme->fromArray($values);
        $layoutsModel = new Layouts();
        $current = false;
        if ($theme->getCurrent() == true) {
            $current = true;
            $theme->setCurrent(false);
        }
        $this->getEntityManager()->persist($theme);
        $result = $this->getEntityManager()->flush();

        if (!empty($values['import_layouts'])) {
            $layoutsModel->importFromTheme($theme, true);
        }

        if($current === true) {
            
            $apiRequest = new \Slys\Api\Request($this,  'sysmap.get-root-identifier');
            $rootNode = $apiRequest->proceed()->getResponse()->getFirst();
 
            $front = false;
            foreach($theme->getLayouts() as $layout) {
                /* @var $layout \Templater\Model\Mapper\Layout */
                foreach($layout->getPoints() as $point) {
                    if($point->getMapId() == $rootNode->getResourceId())
                        $front = true;
                }
            }

            if($front) {
                $this->disableAllThemes();
                $theme->getCurrent(true);
                $this->getEntityManager()->persist($theme);
                $result = $this->getEntityManager()->flush();
            } else {
                throw new \Zend\Layout\Exception('Theme can\'t be activated because, '.
                        'published default layouts not found for this theme');
            }
        }

        return $result;
    }

    /**
     * Generate list of all directories which placed in 'themes' directory
     * @return array
     */
    public function getThemesDirectoriesFromFS()
    {
        $options = \Zend\Controller\Front::getInstance()
                ->getParam("bootstrap")
                ->getOption('templater');
        $dirIterator = new \DirectoryIterator($options['directory']);
        $result = array();
        foreach ($dirIterator as $dir) {
            if ($dir->isDir()
                && !$dir->isDot()
                && strripos($dir->getBasename(), '.') !== 0)
                $result[$dir->getBasename()] = $dir->getBasename();
        }
        return $result;
    }

    /**
     * Delete theme
     * @return boolean
     */
    public function deleteTheme($id)
    {
        $theme = $this->getEntityManager()
                      ->getRepository('\Templater\Model\Mapper\Theme')->find($id);
        if(empty($theme))
            return false;
        if($theme->getCurrent() == true)
            throw new Zend_Exception('You can\'t delete active theme.');
        $this->getEntityManager()->remove($theme);
        return $this->getEntityManager()->flush();
    }

}