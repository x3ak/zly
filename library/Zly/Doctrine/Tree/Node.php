<?php

namespace Zly\Doctrine\Tree;

/**
 * This interface is not necessary but can be implemented for
 * Entities which in some cases needs to be identified as
 * Tree Node
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Zly\Doctrine.Tree
 * @subpackage Node
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface Node
{
    // use now annotations instead of predifined methods, this interface is not necessary

    /**
     * @Zly\Doctrine:TreeLeft
     * to mark the field as "tree left" use property annotation @Zly\Doctrine:TreeLeft
     * it will use this field to store tree left value
     */

    /**
     * @Zly\Doctrine:TreeRight
     * to mark the field as "tree right" use property annotation @Zly\Doctrine:TreeRight
     * it will use this field to store tree right value
     */

    /**
     * @Zly\Doctrine:TreeParent
     * in every tree there should be link to parent. To identify a relation
     * as parent relation to child use @Tree:Ancestor annotation on the related property
     */

    /**
     * @Zly\Doctrine:TreeLevel
     * level of node.
     */
}