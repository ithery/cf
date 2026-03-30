<?php

defined('SYSPATH') or die('No direct access allowed.');

class CModel_Search_Exception_InvalidSearchableModelException extends Exception {
    public static function notAModel($model) {
        return new self("Class `{$model}` is not an Eloquent model.");
    }

    public static function modelDoesNotImplementSearchable($model) {
        return new self("Model `{$model}` is added as a model search aspect but does not implement the `CModel_SearchableInterface` interface.");
    }
}
