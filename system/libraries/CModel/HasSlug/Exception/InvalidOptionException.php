<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 3:17:49 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_HasSlug_InvalidOptionException extends CException {

    public static function missingFromField() {
        return new static('Could not determine which fields should be sluggified');
    }

    public static function missingSlugField() {
        return new static('Could not determine in which field the slug should be saved');
    }

    public static function invalidMaximumLength() {
        return new static('Maximum length should be greater than zero');
    }

}
