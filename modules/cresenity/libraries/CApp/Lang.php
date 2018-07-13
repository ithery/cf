<?php

/**
 * Description of CMLang
 *
 * @author Hery
 */
class CApp_Lang {

    const LANG_SESSION_NAME = '_CAPP_LANG';
    const DEFAULT_LANG = 'id';
    const VAR_KEY_LASTUPDATE = 'language_lastupdate';

    public static function __($message, $params = array(), $lang = null) {
        if ($lang == null) {
            $lang = CMLang::getLang();
        }

        //get translation
        $translation = self::getTranslation($message, $params, $lang);


        return $translation;
    }

    public static function getLang() {
        $session = CSession::instance();
        $lang = $session->get(self::LANG_SESSION_NAME);
        if ($lang == null) {
            $lang = self::DEFAULT_LANG;
        }
        return $lang;
    }

    public static function setLang($langKey) {
        $session = CSession::instance();
        if (self::getLangName($langKey) !== null) {
            $lang = $session->set(self::LANG_SESSION_NAME, $langKey);
        }
        return $lang;
    }

    public static function getTranslation($message, $params = array(), $lang = null) {
        if ($lang == null) {
            $lang = self::getLang();
        }
        $translation = CApp_Lang_Data::getLangDataTranslation($lang, $message);
        if ($translation === null) {
            //save to default language
            CApp_Lang_Data::addLangDataTranslation(CMLang::DEFAULT_LANG, $message, $message);
            $translation = $message;
        }
        if (is_array($params)) {
            $translation = strtr($translation, $params);
        }
        return $translation;
    }

    public static function getLangAvailable() {
        $langAvailable = array(
            'id' => 'Indonesian',
            'en' => 'English',
            'ms' => 'Malaysia',
            'zh' => 'Chinese',
        );
        return $langAvailable;
    }

    public static function isLangAvailable($langKey) {
        return array_key_exists($langKey, self::getLangAvailable());
    }

    public static function getLangName($langKey = null) {
        if ($langKey == null) {
            $langKey = self::getLang();
        }
        $langAvailable = self::getLangAvailable();
        return carr::get($langAvailable, $langKey, null);
    }

}
