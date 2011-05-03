<?php
/**
 * Description
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */
class Eaventity_IndexController extends Zend_Controller_Action
{
    /**
     * Display EAV entity element(s)
     * @paramsform Eaventity_Form_DisplayParams
     * @return void
     */
    public function displayAction()
    {
        $entityId = $this->getRequest()->getParam('entity_id');

        if ($entityId !== null) {
            $entity = Eaventity_Model_Entity::getInstance()->findById($entityId);
            if (empty($entity) === false)
                $this->view->eavItem = Eaventity_Model_Entity::getInstance()->normalizeValues($entity);
        }
    }
}