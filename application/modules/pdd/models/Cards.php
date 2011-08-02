<?php

namespace Pdd\Model;

class Cards extends \Zly\Doctrine\Model
{
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
        $tool->updateSchema($classes, true);
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
          $em->getClassMetadata('\Pdd\Model\Mapper\Card'),
          $em->getClassMetadata('\Pdd\Model\Mapper\Question'),
        );
        
        return $classes;
    }
    
    public function getCards($page = null)
    {
        return $this->getEntityManager()->getRepository('\Pdd\Model\Mapper\Card')->findAll();
    }
    
    public function getCardById($id)
    {
        return $this->getEntityManager()
                    ->getRepository('\Pdd\Model\Mapper\Card')
                    ->findOneBy(array('id'=>$id));
    }
    
    public function saveCard(Mapper\Card $card, $data)
    {
        $card->fromArray($data);       
        $this->getEntityManager()->persist($card);
        $this->getEntityManager()->flush();
        return true;
    }
}