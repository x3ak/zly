<?php

namespace Page\Model;

class Pages extends \Slys\Doctrine\Model
{

    /**
     * @param int $pageId
     * @return Mapper_Page
     */
    public function getPageById($pageId) 
    {
        return $this->getEntityManager()->getRepository('\Page\Model\Mapper\Page')->findOneBy(array('id'=>$pageId));
    }

    /**
     * @param string $sysname
     * @return Mapper_Page
     */
    public function getPageBySysname($sysname) 
    {
        return $this->getEntityManager()->getRepository('\Page\Model\Mapper\Page')->findOneBy(array('sysname'=>$sysname));
    }

    public function getList()
    {
        return $this->getEntityManager()->getRepository('\Page\Model\Mapper\Page')->findAll();
    }
    
    public function savePage(Mapper\Page $page, $data)
    {
        $page->fromArray($data);
        $this->getEntityManager()->persist($page);
        return $this->getEntityManager()->flush();
    }
    
    public function initSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);    
        $tool->createSchema($classes);
        return $this;
    }
    
    public function updateSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->updateSchema($classes);
        return $this;
    }
    
    public function dropSchema()
    {
        $em = $this->getEntityManager();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $this->_getShemaClasses();
        $tool->dropSchema($classes);
        return $this;
    }
    
    protected function _getShemaClasses()
    {
        $em = $this->getEntityManager();
        $classes = array(
          $em->getClassMetadata('Page\Model\Mapper\Page')
        );
        
        return $classes;
    }
}