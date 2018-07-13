<?php

/**
 * Description of Data
 *
 * @author Hery
 */
class CApp_Lang_Data {

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
        $dir = DOCROOT . "application/" . CM::FRONTEND_APPCODE . "/default/lang/";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        return $dir;
    }

    public static function getLangFile($message, $lang = null) {
        if ($lang == null) {
            $lang = CApp_Lang::getLang();
        }
        $dir = self::getLangDir();
        $dir .= $lang . '/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $char = self::getCharForFolder($message);
        $file = $dir . $char . '.php';

        return $file;
    }

    public static function langDataExists($lang, $char, $message) {
        if (self::langDataLoaded($lang, $char)) {
            return isset(self::$langData[$lang][$char][$message]);
        }

        return false;
    }

    public static function langDataLoaded($lang, $char) {
        if (!is_array(self::$langData)) {
            return false;
        }
        if (!isset(self::$langData[$lang])) {
            return false;
        }
        if (!isset(self::$langData[$lang][$char])) {
            return false;
        }
        return true;
    }

    public static function addLangDataTranslation($lang, $message, $translation) {
        $char = self::getCharForFolder($message);

        if (!self::langDataExists($lang, $char, $message)) {
            self::load($char, $lang);
            //$message = addslashes($message);
            self::$langData[$lang][$char][$message] = $translation;
            return self::save($lang, $char);
        }
        return false;
    }

    public static function setLangDataTranslation($lang, $message, $translation) {
        $char = self::getCharForFolder($message);


        self::load($char, $lang);
        //$message = addslashes($message);
        self::$langData[$lang][$char][$message] = $translation;
        return self::save($lang, $char);
    }

    public static function getLangDataChar($lang, $message) {
        $char = self::getCharForFolder($message);
        if (!self::langDataLoaded($lang, $char)) {
            self::load($char, $lang);
        }

        return self::$langData[$lang][$char];
    }

    public static function getLangDataTranslation($lang, $message) {
        $char = self::getCharForFolder($message);
        if (!self::langDataExists($lang, $char, $message)) {
            self::load($char, $lang);
        }
        $translation = null;
        if (isset(self::$langData[$lang][$char][$message])) {
            $translation = self::$langData[$lang][$char][$message];
        }
        return $translation;
    }

    public static function load($char, $lang = null) {

        $char = self::getCharForFolder($char);
        if (!isset(self::$langData)) {
            self::$langData = array();
        }
        if (!isset(self::$langData[$lang])) {
            self::$langData[$lang] = array();
        }
        if (!isset(self::$langData[$lang][$char])) {
            self::$langData[$lang][$char] = array();
        }
        $filename = self::getLangFile($char, $lang);
        if (file_exists($filename)) {
            self::$langData[$lang][$char] = include $filename;
        }

        return self::$langData;
    }

    public static function save($lang = null, $char = null) {
        foreach (self::$langData as $langKey => $subData) {
            if ($lang == null || $lang == $langKey) {
                foreach ($subData as $charKey => $subSubData) {
                    if ($char == null || $char == $charKey) {
                        $filename = self::getLangFile($charKey, $langKey);
                        cphp::save_value($subSubData, $filename);
                    }
                }
            }
        }
        try {
            CMVar::set(CApp_Lang::VAR_KEY_LASTUPDATE, time());
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }

}
