<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 11:19:13 AM
 */
class CTranslation {
    public static function translator($locale = null) {
        if ($locale == null) {
            $locale = CF::getLocale();
        }
        return new CTranslation_Translator(new CTranslation_Loader_FileLoader(new CFile(), 'i18n'), $locale);
    }
}
