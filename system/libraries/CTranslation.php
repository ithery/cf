<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 11:19:13 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTranslation {

    public static function translator() {
        return new CTranslation_Translator(new CTranslation_Loader_FileLoader(new CFile(), DOCROOT . 'system/i18n'));
    }

}
