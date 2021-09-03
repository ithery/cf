<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 2:41:04 AM
 */
class CResources_Exception_InvalidConversion extends CResources_Exception {
    public static function unknownName($name) {
        return new static("There is no conversion named `{$name}`");
    }
}
