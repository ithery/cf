<?php

defined('SYSPATH') or die('No direct access allowed.');

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
