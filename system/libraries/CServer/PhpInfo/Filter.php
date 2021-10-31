<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Jul 30, 2020
 */
class CServer_PhpInfo_Filter {
    const ALL = -1;

    const GENERAL = 1;

    const CREDITS = 2;

    const CONFIGURATION = 4;

    const MODULES = 8;

    const ENVIRONMENT = 16;

    const VARIABLES = 32;

    const LICENSE = 64;

    public static function getList() {
        $oClass = new \ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }
}
