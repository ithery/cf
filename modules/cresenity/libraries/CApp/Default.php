<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 12:33:13 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Default {

    public static function favImageUrl() {
        return curl::base() . 'media/img/favico.png';
    }

    public static function logoImageUrl() {
        return curl::base() . 'media/img/logo.png';
    }

}
