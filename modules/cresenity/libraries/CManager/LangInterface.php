<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 16, 2018, 11:32:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CManager_LangInterface {

    public static function getTranslation($message, $params = array(), $lang = null);

    public static function getLang();

    public static function setLang($langKey);

    public static function getLangAvailable();
}
