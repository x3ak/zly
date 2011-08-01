<?php

namespace Zly\Doctrine\Translatable;

/**
 * This interface is not necessary but can be implemented for
 * Entities which in some cases needs to be identified as
 * Translatable
 * 
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Zly\Doctrine.Translatable
 * @subpackage Translatable
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface Translatable
{
    // use now annotations instead of predifined methods, this interface is not necessary
    
    /**
     * @Zly\Doctrine:TranslationEntity
     * to specify custom translation class use 
     * class annotation @Zly\Doctrine:TranslationEntity(class="your\class")
     */
    
    /**
     * @Zly\Doctrine:Translatable
     * to mark the field as translatable, 
     * these fields will be translated
     */
    
    /**
     * @Zly\Doctrine:Locale OR @Zly\Doctrine:Language
     * to mark the field as locale used to override global
     * locale settings from TranslationListener
     */
}