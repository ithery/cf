<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 2:41:04 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_Exception_InvalidConversion extends CResources_Exception {

    public static function unknownName($name) {
        return new static("There is no conversion named `{$name}`");
    }

}
