<?php

/**
 * Description of Data
 *
 * @author Hery
 */
class CManager_Lang_Data {

    protected static $langData = array();

    protected static function getCharForFolder($message) {
        $char = '_';
        if (strlen($message) > 0) {
            $char = strtolower(substr($message, 0, 1));
            if (!preg_match("/^[a-zA-Z]$/", $char)) {
                $char = '_';
            }
        }
        return $char;
    }

    public static function getLangDir() {

        $dir = DOCROOT . "application/" . CF::appCode() . "/default/lang/";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        return $dir;
    }

    public static function getLangFile($message, $lang = null) {
        if ($lang == null) {
            $lang = CApp_Lang::getLang();
        }
        $dir = static::getLangDir();
        $dir .= $lang . '/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $char = static::getCharForFolder($message);
        $file = $dir . $char . '.php';

        return $file;
    }

    public static function langDataExists($lang, $char, $message) {
        if (static::langDataLoaded($lang, $char)) {
            return isset(static::$langData[$lang][$char][$message]);
        }

        return false;
    }

    public static function langDataLoaded($lang, $char) {
        if (!is_array(static::$langData)) {
            return false;
        }
        if (!isset(static::$langData[$lang])) {
            return false;
        }
        if (!isset(static::$langData[$lang][$char])) {
            return false;
        }
        return true;
    }

    public static function addLangDataTranslation($lang, $message, $translation) {
        $char = static::getCharForFolder($message);

        if (!static::langDataExists($lang, $char, $message)) {
            static::load($char, $lang);
            //$message = addslashes($message);
            static::$langData[$lang][$char][$message] = $translation;
            return static::save($lang, $char);
        }
        return false;
    }

    public static function setLangDataTranslation($lang, $message, $translation) {
        $char = static::getCharForFolder($message);


        static::load($char, $lang);
        //$message = addslashes($message);
        static::$langData[$lang][$char][$message] = $translation;
        return static::save($lang, $char);
    }

    public static function getLangDataChar($lang, $message) {
        $char = static::getCharForFolder($message);
        if (!static::langDataLoaded($lang, $char)) {
            static::load($char, $lang);
        }

        return static::$langData[$lang][$char];
    }

    public static function getLangDataTranslation($lang, $message) {
        if(!CF::appCode()) {
            return $message;
        }
        $char = static::getCharForFolder($message);
        if (!static::langDataExists($lang, $char, $message)) {
            static::load($char, $lang);
        }
        $translation = null;
        if (isset(static::$langData[$lang][$char][$message])) {
            $translation = static::$langData[$lang][$char][$message];
        }
        return $translation;
    }

    public static function load($char, $lang = null) {

        $char = static::getCharForFolder($char);
        if (!isset(static::$langData)) {
            static::$langData = array();
        }
        if (!isset(static::$langData[$lang])) {
            static::$langData[$lang] = array();
        }
        if (!isset(static::$langData[$lang][$char])) {
            static::$langData[$lang][$char] = array();
        }
        $filename = static::getLangFile($char, $lang);
        if (file_exists($filename)) {
            static::$langData[$lang][$char] = include $filename;
        }

        return static::$langData;
    }

    public static function save($lang = null, $char = null) {
        foreach (static::$langData as $langKey => $subData) {
            if ($lang == null || $lang == $langKey) {
                foreach ($subData as $charKey => $subSubData) {
                    if ($char == null || $char == $charKey) {
                        $filename = static::getLangFile($charKey, $langKey);
                        cphp::save_value($subSubData, $filename);
                    }
                }
            }
        }

        return true;
    }

}
