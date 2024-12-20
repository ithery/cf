<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTranslation {
    protected static $translator;

    /**
     * @param string $locale
     *
     * @return CTranslation_Translator
     */
    public static function translator($locale = null) {
        if ($locale == null) {
            $locale = CF::getLocale();
        }
        if (!is_array(static::$translator)) {
            static::$translator = [];
        }

        if (!isset(static::$translator[$locale])) {
            static::$translator[$locale] = new CTranslation_Translator(new CTranslation_Loader_FileLoader(new CFile(), 'i18n'), $locale);
        }

        return static::$translator[$locale];
    }

    /**
     * @return CTranslation_Manager
     */
    public static function manager() {
        return CTranslation_Manager::instance();
    }
}
