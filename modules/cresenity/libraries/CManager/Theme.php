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

    public static function getThemeData() {
        $theme = self::getCurrentTheme();
        $themeFile = CF::get_file('themes', $theme);
        $themeAllData = null;
        if (file_exists($themeFile)) {
            $themeAllData = include $themeFile;
        }
        return $themeAllData;
    }

    public static function getData($key) {
        $themeAllData = self::getThemeData();
        $themeData = carr::get($themeAllData, 'data');
        return carr::path($themeData, $key);
    }

    public static function getThemePath() {
        $theme_path = '';
        $theme = self::getCurrentTheme();
        $themeFile = CF::get_file('themes', $theme);
        if (file_exists($theme_file)) {
            $theme_data = include $theme_file;
            $theme_path = carr::get($theme_data, 'theme_path');
            if ($theme_path == null) {
                $theme_path = '';
            } else {
                $theme_path .= '/';
            }
        }
        return $theme_path;
    }

}
