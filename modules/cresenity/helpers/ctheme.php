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
        
        public static function get_theme_list(){
            return self::$themes;
        }
        
        public static function get_default_theme(){
            return ccfg::get('theme');
        }
        
        public static function get_current_theme(){
            $theme = Session::instance()->get('theme');
            if ($theme == null) {
                $theme = ccfg::get('theme');
                if ($theme == null) $theme = 'cresenity';
            }
            return $theme;
        }
        
        public static function set_theme($theme){
            Session::instance()->set( 'theme',$theme);
        }
    }
    