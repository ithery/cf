<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 12:33:13 AM
 */
class CApp_Default {
    public static function favImageUrl() {
        return curl::base() . 'media/img/favico.png';
    }

    public static function logoImageUrl() {
        return curl::base() . 'media/img/logo.png';
    }
}
