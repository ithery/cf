<?php

defined('SYSPATH') or die('No direct access allowed.');

class CHelper_Domain {
    public static function getTopLevelDomain($domain) {
        return CServer_Domain::getTopLevelDomain($domain);
    }

    public static function isTopLevelDomain($domain) {
        return CServer_Domain::getTopLevelDomain($domain);
    }

    public static function isDomainExtension($domain) {
        return CServer_Domain::isDomainExtension($domain);
    }

    public static function getTLDs() {
        return CServer_Domain::getTLDs();
    }
}
