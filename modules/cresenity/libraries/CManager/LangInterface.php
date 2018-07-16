<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 16, 2018, 11:32:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CManager_LangInterface {

    /**
     * main method for the translation logic to be implemented
     * 
     * @param string $message string to be translated
     * @param array $params parameter for variable translation
     * @param string $lang optional, if null must call the function getLang
     */
    public static function getTranslation($message, $params = array(), $lang = null);

    /**
     * Get Current Lang
     * @return string
     */
    public static function getLang();

    /**
     * Set Current Lang
     * @param string $langKey
     */
    public static function setLang($langKey);

    /**
     * Get current available lang 
     * must return assoc array with array key langCode and array value langName
     * 
     * @return array
     */
    public static function getLangAvailable();
}
