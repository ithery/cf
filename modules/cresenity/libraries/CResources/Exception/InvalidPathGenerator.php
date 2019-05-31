<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 1:30:53 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_Exception_InvalidPathGenerator extends CResources_Exception {

    public static function doesntExist($class) {
        return new static("Class {$class} doesn't exist");
    }

    public static function isntAPathGenerator($class) {
        return new static("Class {$class} must implement `CResources_PathGeneratorInterface`");
    }

}
