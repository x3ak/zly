<?php

/**
 * Zly
 *
 * @version    $Id: Roles.php 1232 2011-04-17 21:00:36Z deeper $
 */
namespace User\Model;

class Roles extends \Zly\Doctrine\Model
{
    /**
     * Return collection of all roles
     * @return array
     */
    public function getRoles()
    {
        return $this->getEntityManager()
                    ->getRepository('User\Model\Mapper\Role')
                    ->findAll();
    }

    /**
     * Return Roles list
     * @return array
     */
    public function getlist()
    {
        return $this->getRoles();
    }
    
    /**
     * Return collection of rules for provided role and resources
     * @param string $role
     * @param string $resources
     * @return array 
     */
    public function getRulesByRoleAndResources($role, $resources)
    {
        return $this->getEntityManager()
                    ->getRepository('\User\Model\Mapper\Rule')
                    ->getRoleRules($role, $resources);
    }

    /**
     * Return user role by Id of empty if not found and request update
     * @param int $id
     * @param boolean $forUpdate
     * @return \User\Model\Mapper\Role 
     */
    public function getRole($id, $forUpdate = false)
    {
        $role = $this->getEntityManager()->find('\User\Model\Mapper\Role', $id);
        if ($forUpdate && empty($role))
            $role = new Mapper\Role();
        return $role;
    }

    /**
     * Roles pager
     * @param int $page
     * @param int $maxPerPage
     * @return Doctrine_Pager
     */
    public function getRolesPaginator($pageNumber = 1, $itemCountPerPage = 20)
    {
        $repo = $this->getEntityManager()->getRepository('\User\Model\Mapper\Role');
        $paginator = new \Zend\Paginator\Paginator($repo->getPaginatorAdapter());
        $paginator->setCurrentPageNumber($pageNumber)->setItemCountPerPage($itemCountPerPage);
        return $paginator;
    }

    /**
     * Saving role
     * @param Mapper\Role $role
     * @param array $values
     * @return boolean
     */
    public function saveRole(Mapper\Role $role, $values)
    {
        $values['resources'] = (array)$values['resources']; 
        $role->fromArray($values);

        foreach($role->getRules() as $rule) {            
            if(!in_array($rule->getResourceId(), $values['resources'])) {
                $this->getEntityManager()->remove($rule);
            }
        }
        $this->getEntityManager()->persist($role);               
        
        if(!empty($values['resources'])) {
            foreach($values['resources'] as $key=>$mapId) {
                $rule = $this->getEntityManager()
                             ->getRepository('\User\Model\Mapper\Rule')
                             ->findOneBy(array('resource_id'=>$mapId, 'role_id'=>$role->getId()));  
                if(empty($rule)) {
                    $rule = new Mapper\Rule();
                    $rule->setResourceId($mapId);
                    $rule->setRule('allow');    
                    $rule->setRoleId($role->getId());
                    $rule->setRole($role);
                    $this->getEntityManager()->persist($rule);                                                          
                }
            }
        }
        
        return $this->getEntityManager()->flush();
    }
}
