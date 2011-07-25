<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Model
 *
 * @author deep
 */
namespace Zly\Doctrine;

abstract class Model
{

    /**
     * Doctrine 2 Entity manager
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em = false;

    /**
     * Return Doctrine 2 entity manager
     * for current connection
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if(empty($this->_em))
            $this->_setEntityManager();
        return $this->_em;
    }

    /**
     * Set entity manager to
     * @return Zly_Doctrine_Model
     */
    private function _setEntityManager()
    {
        $doctrine = \Zend\Controller\Front::getInstance()
                        ->getParam('doctrine');

        if(!empty($doctrine) && $doctrine instanceof \Zly\Application\Resource\Doctrine) {
            $this->_em = $doctrine->getEntityManager();
        }

        return $this;
    }
}
