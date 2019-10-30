<?php

/**
 * Description of CManager_Theme
 *
 * @author Hery
 */
class CManager_Theme {

    /**
     *
     * @var callable
     */
    protected static $themeCallback;
    protected static $themeData = array();

    public static function setThemeCallback(callable $themeCallback) {
        self::$themeCallback = $themeCallback;
    }

    public static function getDefaultTheme() {
        $theme = CF::theme();
        if (strlen(ccfg::get('theme')) > 0) {
            $theme = ccfg::get('theme');
        }
        return $theme;
    }

    public static function getCurrentTheme() {
        $theme = CSession::instance()->get('theme');
        if ($theme == null) {
            $theme = self::getDefaultTheme();
            if ($theme == null) {
                $theme = 'cresenity';
            }
        }

        if (self::$themeCallback != null && is_callable(self::$themeCallback)) {
            $theme = call_user_func(self::$themeCallback, $theme);
        }
        return $theme;
    }

    public static function setTheme($theme) {
        CSession::instance()->set('theme', $theme);
    }

    public static function getThemeData($theme = null) {
        if ($theme == null) {
            $theme = self::getCurrentTheme();
        }
        if (!isset(self::$themeData[$theme])) {
            $themeFile = CF::get_file('themes', $theme);
            $themeAllData = null;
            if (file_exists($themeFile)) {
                $themeAllData = include $themeFile;
            }
            self::$themeData[$theme] = $themeAllData;
        }
        return self::$themeData[$theme];
    }

    public static function setThemeData($themeData, $theme = null) {
        if ($theme == null) {
            $theme = self::getCurrentTheme();
        }
        self::$themeData[$theme] = $themeData;

        return self::$themeData[$theme];
    }

    public static function getData($key,$default=null) {
        $themeAllData = self::getThemeData();
        $themeData = carr::get($themeAllData, 'data',$default);
        return carr::get($themeData, $key,$default);
    }

    public static function getThemePath() {
        $themePath = '';
        $theme = self::getCurrentTheme();
        $themeFile = CF::get_file('themes', $theme);
        if (file_exists($themeFile)) {
            $themeData = include $themeFile;
            $themePath = carr::get($themeData, 'theme_path');
            if ($themePath == null) {
                $themePath = '';
            } else {
                $themePath .= '/';
            }
        }
        return $themePath;
    }

}
