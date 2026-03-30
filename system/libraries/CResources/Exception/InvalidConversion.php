<?php

defined('SYSPATH') or die('No direct access allowed.');

class CResources_Exception_InvalidConversion extends CResources_Exception {
    public static function unknownName($name) {
        return new static("There is no conversion named `{$name}`");
    }
}
