<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 2, 2019, 3:03:47 AM
 */
class CResources_Exception_FileCannotBeAdded_FileDoesNotExist extends CResources_Exception_FileCannotBeAdded {
    public static function create($path) {
        return new static("File `{$path}` does not exist");
    }
}
