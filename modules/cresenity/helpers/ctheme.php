<?php

/**
 *
 * @author Raymond Sugiarto
 * @since  Mar 15, 2016
 */
class ctheme {

    public static $themes = array(
        'cresenity' => 'Default',
        'ittron-app' => 'ITtron Theme'
    );

    public static function get_theme_list() {
        return self::$themes;
    }

    public static function get_default_theme() {
        return ccfg::get('theme');
    }

    public static function get_current_theme() {
        $theme = CSession::instance()->get('theme');
        if ($theme == null) {
            $theme = ccfg::get('theme');
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
