<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 28, 2019, 9:43:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Search_Exception_InvalidModelSearchAspectException extends Exception {

    public static function noSearchableAttributes($model) {
        return new self("Model search aspect for `{$model}` doesn't have any searchable attributes.");
    }

}
