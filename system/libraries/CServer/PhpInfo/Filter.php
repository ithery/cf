<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Jul 30, 2020 
 * @license Ittron Global Teknologi
 */
class CServer_PhpInfo_Filter {

    const All = -1;
    const General = 1;
    const Credits = 2;
    const Configuration = 4;
    const Modules = 8;
    const Environment = 16;
    const Variables = 32;
    const License = 64;

    public static function getList() {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

    

}
