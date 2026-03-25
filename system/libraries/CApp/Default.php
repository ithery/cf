<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Default {
    public static function favImageUrl() {
        return curl::base() . 'media/img/favico.png';
    }

    public static function logoImageUrl() {
        return curl::base() . 'media/img/logo.png';
    }
}
