<?php

defined('SYSPATH') or die('No direct access allowed.');

class CModel_Search_Exception_InvalidModelSearchAspectException extends Exception {
    public static function noSearchableAttributes($model) {
        return new self("Model search aspect for `{$model}` doesn't have any searchable attributes.");
    }
}
