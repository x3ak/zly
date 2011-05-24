<?php

/**
 * SlyS
 * 
 * This is a class generated with Zend_CodeGenerator.
 * 
 * @version $Id: Generator.php 1158 2011-02-02 09:48:32Z deeper $
 * @license New BSD
 */

namespace \Sysmap\Model\Mapper;

class Sysmap
{
	public function getMapIdentifier()
    {
        return $this->hash;
    }

    /**
     * Return current MCA as a request object
     * @return Zend_Controller_Request_Simple
     */
    public function toRequest()
    {
        if (empty($this->mca) and $this->level == 4)
            $details = \Sysmap\Model\Map::getInstance()->parseMcaFormat($this->getNode()->getParent()->mca);
        else
            $details = \Sysmap\Model\Map::getInstance()->parseMcaFormat($this->mca);

        return new \Zend\Controller\Request\Simple($details['action'], $details['controller'], $details['module'], (array)$this->params);
    }
}