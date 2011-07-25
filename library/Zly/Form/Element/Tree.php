<?php

/**
 * Slys
 *
 * @author  Evgheni Poleacov <evgheni.poelacov@gmail.com>
 * @version $Id: Tree.php 998 2011-01-06 16:14:45Z deeper $
 */

namespace Slys\Form\Element;

class Tree extends \Zend\Form\Element\Multi
{
    /**
     * Disable autoregister inArray validator
     * @var bool
     */
    protected $_registerInArrayValidator = false;

    /**
     * Use Tree view helper
     * @var string
     */
    public $helper = 'Tree';

    /**
     * Element config
     * 
     * @var array
     */
    protected $_config = array();

    /**
     * Value pattern separator
     * 
     * @var string
     */
    protected $_patternSeparator = ':';

    /**
     * Disabled conditions
     * 
     * @var string
     */
    protected $_disableConditions = array();

    /**
     * Key which indicate that field is childrens array
     * @var string
     */
    protected $_childrensKey = '__children';

    /**
     * Option value key
     * @var string
     */
    protected $_valueKey = null;

    /**
     * Option title key
     * @var string
     */
    protected $_titleKey = null;

    /**
     * Tree root label
     * @var string
     */
    protected $_rootLabel;

    /**
     * Tree root value
     * @var string
     */
    protected $_rootValue;

    /**
     * Switch tree to multiple select mode
     * @param boolean $multiple
     * @return Slys_Form_Element_Tree
     */
    public function setMultiple($multiple = true)
    {
        $this->setAttrib('multiple', $multiple);
        return $this;
    }

    /**
     * Add disabled node condition based on Zend_Validation
     *
     * @param string $field
     * @param Zend_Validate_Interface $validator
     * @return Slys_Form_Element_Tree
     */
    public function addDisableCondition($field, \Zend\Validator\AbstractValidator $validator = null)
    {
        $this->_disableConditions[$field][] = $validator;
        return $this;
    }

    /**
     * Set root element properties
     *
     * @param string $label
     * @param mixed $value
     * @return Slys_Form_Element_Tree
     */
    public function setRoot($label, $value = null)
    {
        $this->_rootLabel = $label;
        $this->_rootValue = $value;
        return $this;
    }

    /**
     * Set key which would be represented like tree item title
     * @param string $key
     * @return Slys_Form_Element_Tree
     */
    public function setTitleKey($key)
    {
        $this->_titleKey = $key;
        return $this;
    }

    /**
     * Return opyion title key
     * @return string
     */
    public function getTitleKey()
    {
        return $this->_titleKey;
    }

    /**
     * Set key which would be represented like tree item value
     * @param string $key
     * @return Slys_Form_Element_Tree
     */
    public function setValueKey($key, $defaultValue = null)
    {
        $this->_valueKey = $key;
        return $this;
    }

     /**
     * Get key which would be represented like tree item value
     */
    public function getValueKey()
    {
        return $this->_valueKey;
    }

    /**
     * Set key which would be represented like tree item value
     * @param string $key
     * @return Slys_Form_Element_Tree
     */
    public function setChildrensKey($key)
    {
        $this->_childrensKey = $key;
        return $this;
    }

    /**
     * Get key which would be represented like tree item value
     * @param string $key
     * @return Slys_Form_Element_Tree
     */
    public function getChildrensKey()
    {
        return $this->_childrensKey;
    }

    /**
     * Set value pattern separator
     * @param string $separator
     * @return Slys_Form_Element_Tree 
     */
    public function setValuePatternSeparator($separator)
    {
        $this->_patternSeparator = $separator;
        return $this;
    }

    /**
     * Return value pattern separator
     * @return string
     */
    public function getValuePatternSeparator()
    {
        return $this->_patternSeparator;
    }

    /**
     * Return rendred element
     *
     * @param Zend_View_Abstract $view
     * @return string
     */
    public function render(\Zend\View\Renderer $view = null)
    {
        $preparedOptions = $this->_prepareOptions($this->options);
        $this->options = $this->_addRoot($preparedOptions);        
        return parent::render($view);
    }


    /**
     * Prepare attributes of options as
     * indicated by element configuration
     *
     * @param array $options
     */
    protected function _prepareOptions($options)
    {
       $prepared = array();       
       foreach ($options as $key => $option) {
            
            $value = $this->_parsePattern($option, $this->getValueKey());
            $title = $this->_parsePattern($option, $this->getTitleKey());
            $this->_checkDisabled($option, $value);
            $prepared[$value] = $title;
            
            if(is_array($option) && !empty($option[$this->_childrensKey])
                && is_array($option[$this->_childrensKey])) {                
                    
                    $prepared[$value.'_childrens'] = $this->_prepareOptions($option[$this->_childrensKey]);
                    
            } elseif(is_object($option) && !empty($option->{$this->_childrensKey}) 
                && is_array($option->{$this->_childrensKey})) {

                $prepared[$value.'_childrens'] = $this->_prepareOptions($option->{$this->_childrensKey});
                
            }
            
            
        }

        return $prepared;
    }

    /**
     * Check and set disabled options to element attributes
     * @param array $option
     * @param mixed $value
     * @return boolean
     */
    protected function _checkDisabled($option, $value)
    {
        $disabled = false;
        if(!empty($this->_disableConditions)) {
            foreach($this->_disableConditions as $field=>$validators) {
                $disables = array();
                foreach($validators as $validator) {                    
                    if ($validator instanceof \Zend\Validator\Validator && is_array($option) && isset($option[$field]))
                        $disables[] = $validator->isValid($option[$field]);
                    elseif ($validator instanceof \Zend\Validator\Validator && is_object($option) && isset($option->$field)) {
                        $disables[] = $validator->isValid($option->$field);
                    }
                    elseif(is_array($option) && isset($option[$field]))
                        $disables[] = true;
                    elseif(is_object($option) && isset($option->$field)) {
                        $disables[] = true;
                    }
                }

                if(in_array(true, $disables))
                    $disabled = true;
                else
                    $disabled = false;
            }

            if($disabled) {
                $disableAttr = $this->getAttrib('disable');
                if(!empty($disableAttr)) {
                    if(is_array($disableAttr)) {
                        $disableAttr[] = $value;
                    } elseif(is_string($disableAttr)) {
                        $disableAttr = array($disableAttr, $value);
                    }
                } else {
                    $disableAttr = array($value);
                }
                $this->setAttrib('disable', $disableAttr);
            }
        }
        return $disabled;
    }

    /**
     * Patterns parser
     *
     * @param array $option
     * @param string $pattern
     * @return string
     */
    protected function _parsePattern($option, $pattern)
    {
        $parts = explode($this->_patternSeparator, $pattern);
        $values = array();
        foreach ($parts as $part) {
            if (is_array($option) && !empty($option[$part]))
                $values[] = $option[$part];
            elseif($option instanceof \stdClass && !empty($option->$part)) {
                $values[] = $option->$part;
            }
        }
        $value = implode($this->_patternSeparator, $values);

        return $value;
    }

    /**
     * Add root node to options if that required
     * @return array|boolean
     */
    protected function _addRoot($options)
    {
        if(!empty($this->_rootLabel) && $this->_rootValue !== null) {
            $root = array($this->_rootValue=>$this->_rootLabel);
        } elseif(!empty($this->_rootLabel) && $this->_rootValue === null) {
            $root = array($this->_rootLabel);
        } else {
            $root = false;
        }

        if($root) {
            $childrensKey = uniqid();
            $root[$childrensKey] = $options;
            return $root;
        } else {
            return $options;
        }
    }
}