<?php

namespace Zly\Doctrine\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Loggable annotation for Loggable behavioral extension
 *
 * @Annotation
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Zly\Doctrine.Mapping.Annotation
 * @subpackage Loggable
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class Loggable extends Annotation
{
    public $logEntryClass;
}

