<?php

/**
 * Slys
 *
 * @version    $Id: Layouts.php 1134 2011-01-28 14:31:15Z deeper $
 */
namespace Templater\Model;

class Layouts extends \Slys\Doctrine\Model
{

    /**
     * Return all layouts collection
     * @return Doctrine_Collection
     */
    public function getlist()
    {
        return $this->getEntityManager()->getRepository('\Templater\Model\Mapper\Layout')->findAll();
    }

    /**
     * Return single layout by id
     * @param int $id
     * @return Templater_Model_Mapper_Layout
     */
    public function getLayout($id, $forEdit = false)
    {
        if(empty($id))
            return new Mapper\Layout();

        $layout = $this->getEntityManager()->getRepository('\Templater\Model\Mapper\Layout')->getLayoutWithLayoutPoints($id);

        if(empty($layout))
            return new Mapper\Layout();
        else
            return $layout;
    }
    
    /**
     * @return  \Templater\Model\Mapper\Layout
     */
    public function getDefaultLayout()
    {
        return $this->getEntityManager()
                    ->getRepository('\Templater\Model\Mapper\Layout')
                    ->getDefaultLayout();
    }

    /**
     * Return layouts list pager
     *
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getLayoutsPaginator($pageNumber = 1, $itemCountPerPage = 20, array $where = array())
    {
        $repo = $this->getEntityManager()->getRepository('Templater\Model\Mapper\Layout');
        $paginator = new \Zend\Paginator\Paginator($repo->getPaginatorAdapter());
        $paginator->setCurrentPageNumber($pageNumber)->setItemCountPerPage($itemCountPerPage);
        return $paginator;
    }

    /**
     * Return list of layouts found in Theme directory
     * and save it if not found in DB
     *
     * @param Templater_Model_Mapper_Theme $theme
     * @return array
     */
    public function importFromTheme(Mapper\Theme $theme, $import = false)
    {
        $options = \Zend\Controller\Front::getInstance()
                ->getParam("bootstrap")
                ->getOption('templater');
        
        $path = realpath($options['directory'] . DIRECTORY_SEPARATOR .
            $theme->getName(). DIRECTORY_SEPARATOR .
            $options['layout']['directory']);
        if(empty($path))
            return false;

        $layouts = $this->getLayoutsFiles($path);
        
        $apiRequest = new \Slys\Api\Request($this,  'sysmap.get-root-identifier');
        $rootNode = $apiRequest->proceed()->getResponse()->getFirst();
            
        if($import)
            foreach (array_keys($layouts) as $name) {
                $exist = $this->getEntityManager()->getRepository('\Templater\Model\Mapper\Layout')
                        ->findOneBy(array('theme_id'=>$theme->getId(), 'name'=>$name));
               
                if (empty($exist)) {
                    $layout = new Mapper\Layout();
                    $layout->setName($name);
                    $layout->setTheme($theme);
                    $layout->setTitle(ucfirst($name));
                    $layout->setPublished(true);
                    $this->getEntityManager()->persist($layout);
                    
                    if ($name == $options['layout']['default']) {

                        $layPoint = new Mapper\LayoutPoint();
                        $layPoint->setMapId($rootNode->getResourceId());
                        $layPoint->setLayout($layout);
                        $this->getEntityManager()->persist($layPoint);
                    }
                }
            }
        
        $this->getEntityManager()->flush();
        return $layouts;
    }

    /**
     * Return list of files which found in tempalte directory
     * @param string $path
     * @return array
     */
    public function getLayoutsFiles($path)
    {
        $result = array();
        $dirIterator = new \DirectoryIterator($path);
        foreach ($dirIterator as $dir) {
            if (!$dir->isDir() && $dir->isFile()
                && strripos($dir->getBasename(), '.') !== 0) {
                $result[$dir->getBasename('.phtml')] = $dir->getBasename();
            }
        }
        return $result;
    }

    /**
     * Save layout
     * @param Templater_Model_Mapper_Layout $layout
     * @param array $values
     * @return boolean
     */
    public function saveLayout(Mapper\Layout $layout, $values)
    {    
        $layout->fromArray($values);
        
        $id = $layout->getId();
        
        if(!empty($id)) {
            $this->getEntityManager()->getRepository('\Templater\Model\Mapper\LayoutPoint')
                ->deleteUnusedPoints($layout->getId(), $values['map_id']);
        } else {
            $theme = $this->getEntityManager()->getRepository('\Templater\Model\Mapper\Theme')
                          ->find($values['theme_id']);
            $layout->setTheme($theme);
        }
        
        $this->getEntityManager()->persist($layout);
        
        if(!empty($values['map_id'])) {
            foreach($values['map_id'] as $key=>$mapId) {
                $repo = $this->getEntityManager()->getRepository('\Templater\Model\Mapper\LayoutPoint');
                $layPoint = $repo->findOneBy(array('map_id'=>$mapId, 'layout_id'=>$layout->getId()));
                
                if($layPoint) {
                    $layPoint = new Mapper\LayoutPoint();
                    $layPoint->setMapId($mapId);
                    $layPoint->setLayoutId($layout->getId());
                    $this->getEntityManager()->persist($layPoint);
                } 
            }

        }

        return $this->getEntityManager()->flush();
    }

    /**
     * Delete layout
     * @param Mapper\Layout $layout
     * @return boolean
     */
    public function deleteLayout(Mapper\Layout $layout, \Zend\Controller\Request\AbstractRequest $request)
    {
        $currentLayout = $this->getEntityManager()->getRepository('\Templater\Model\Mapper\Layout')
                ->getCurrentLayout($request);
        if($currentLayout->getId() == $layout->getId())
            throw new \Zend\Layout\Exception('You can\'t delete current layout');
        $this->getEntityManager()->remove($layout);
        return $this->getEntityManager()->flush();
    }

}