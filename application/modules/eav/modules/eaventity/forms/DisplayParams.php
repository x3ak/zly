<?php
/**
 * Description
 *
 * @author Serghei Ilin <criolit@gmail.com>
 * @version $Id$
 */
class Eaventity_Form_DisplayParams extends Zend_Form_SubForm
{
    /**
     * Init form
     * @return void
     */
    public function init()
    {
        $this->setMethod(self::METHOD_POST);

        $typeId = new Zend_Form_Element_Select('type_id');
        $typeId->setRequired()
               ->setAllowEmpty(false)
               ->setLabel('Select type');

        $types = Eavtype_Model_Type::getInstance()->getList(Doctrine_Core::HYDRATE_ARRAY);

        if (empty($types) === false) {
            foreach($types as $type)
                $typeId->addMultiOption($type['id'], $type['name']);
        }

        $this->addElement($typeId);
    }

    /**
     * Populate form
     *
     * Proxies to {@link setDefaults()}
     *
     * @param  array $values
     * @return Zend_Form
     */
    public function populate(array $data)
    {
        $this->_appendSubForm( $this->_getTypeId($data), $this->_getEntityId($data) );
        return parent::populate($data);
    }

    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {
        $this->_appendSubForm( $this->_getTypeId($data), $this->_getEntityId($data) );
        return parent::isValid($data);
    }

    public function setDefaults(array $defaults)
    {
        $this->_appendSubForm( $this->_getTypeId($defaults), $this->_getEntityId($defaults) );
        return parent::setDefaults($defaults);
    }

    protected function _getTypeId($data)
    {
        $typeId = null;

        $subformName = $this->getName();

        if (empty($subformName) === false and empty($data[$subformName]['type_id']) === false)
            $typeId = $data[$subformName]['type_id'];
        elseif (empty($data['type_id']) === false)
            $typeId = $data['type_id'];
        else
            $typeId = $this->getElement('type_id')->getValue();

        return $typeId;
    }

    protected function _getEntityId($data)
    {
        $entityId = null;

        $subformName = $this->getName();

        if (empty($subformName) === false and empty($data[$subformName]['entity_id']) === false)
            $entityId = $data[$subformName]['entity_id'];
        elseif (empty($data['entity_id']) === false)
            $entityId = $data['entity_id'];

        return $entityId;
    }

    protected function _appendSubForm($typeId, $entityId)
    {
        if (empty($typeId))
            return;

        $entityList = new Zend_Form_Element_Select('entity_id');
        $entityList->setLabel('entity')
                   ->setRequired(true)
                   ->setAllowEmpty(false);

        $entities = Eaventity_Model_Entity::getInstance()->findByType($typeId);

        if (empty($entities) === false)
            foreach($entities as $entity)
                $entityList->addMultiOption($entity['id'], $entity['entity_name']);

        $entityList->setValue($entityId);

        $this->addElement($entityList);
    }
}