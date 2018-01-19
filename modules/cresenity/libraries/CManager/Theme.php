<?php

/**
 * Description of CManager_Theme
 *
 * @author Hery
 */
class CManager_Theme {

    public static $themes = array(
        'cresenity' => 'Default',
        'ittron-app' => 'ITtron Theme'
    );

    public static function get_theme_list() {
        return self::$themes;
    }

    public static function get_default_theme() {
        $theme = CF::theme();
        if (strlen(ccfg::get('theme')) > 0) {
            $theme = ccfg::get('theme');
        }
        return $theme;
    }

    public static function get_current_theme() {
        $theme = CSession::instance()->get('theme');
        if ($theme == null) {
            $theme = self::get_default_theme();
            if ($theme == null)
                $theme = 'cresenity';
        }
        return $theme;
    }

    public static function set_theme($theme) {
        CSession::instance()->set('theme', $theme);
    }

    public static function get_theme_path() {
        $theme_path = '';
        $theme = ctheme::get_current_theme();
        $theme_file = CF::get_file('themes', $theme);
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
