<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 3:03:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_Exception_FileCannotBeAdded_FileDoesNotExist extends CResources_Exception_FileCannotBeAdded {

    public static function create( $path) {
        return new static("File `{$path}` does not exist");
    }

}
