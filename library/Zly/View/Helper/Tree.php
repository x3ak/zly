<?php

/**
 * SlyS Framework
 *
 * @category   SlyS
 * @package    Zly_View
 * @subpackage Helper
 * @version    $Id: Tree.php 1003 2011-01-11 10:43:38Z deeper $
 */

namespace Zly\View\Helper;

class Tree extends \Zend\View\Helper\FormRadio
{
    /**
     * Input type to use
     * @var string
     */
    protected $_inputType = 'radio';

    /**
     * Display structured options as tree list
     * @param string $name
     * @param mixed $value
     * @param array $attribs
     * @param array $options
     * @param string $listsep
     * @return string
     */
    public function direct($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n")
    {

        if(isset($attribs['__isChild'])) {
             if($attribs['__isChild'])
                $class = 'children-level';
            unset($attribs['__isChild']);
        }
        else
            $class = 'root-level';

        if(isset($attribs['multiple'])) {
            if($attribs['multiple']) {
                $this->_inputType = 'checkbox';
                $name = $name.'[]';
            }
            unset($attribs['multiple']);
        }

        $html = '<ul class="tree ' . $class . '">';

        foreach ($options as $okey => $option) {
            $html .= '<li>';
            
            if (is_string($option)) {
                
                $html .= parent::direct($name, $value, $attribs, array($okey => $option), $listsep);
            } elseif(is_array($option)) {

                $nextAttribs = $attribs;
                $nextAttribs['__isChild'] = true;
                $html .= $this->direct($name, $value, $nextAttribs, $option, $listsep);
            }
            $html .= '</li>';
        }

        $html .= '</ul>';
        
        return $html;
    }

}