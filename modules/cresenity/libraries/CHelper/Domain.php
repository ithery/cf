<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 16, 2019, 12:55:09 AM
 */
class CHelper_Domain {
    public static function getTopLevelDomain($domain) {
        $domainParts = explode('.', $domain);

        while (count($domainParts) > 1) {
            $previousDomainParts = $domainParts;

            array_shift($previousDomainParts);
            $currentDomain = implode('.', $previousDomainParts);

            if (static::isTopLevelDomain($currentDomain)) {
                return $currentDomain;
            }
            $domainParts = explode('.', $currentDomain);
        }
        return false;
    }

    public static function isTopLevelDomain($domain) {
        $domainParts = explode('.', $domain);
        if (count($domainParts) == 1) {
            return false;
        }

        $previousDomainParts = $domainParts;
        array_shift($previousDomainParts);

        $tld = implode('.', $previousDomainParts);
        return static::isDomainExtension($tld);
    }

    public static function isDomainExtension($domain) {
        $tlds = static::getTLDs();

        /**
         * Direct hit
         */
        if (in_array($domain, $tlds)) {
            return true;
        }

        if (in_array('!' . $domain, $tlds)) {
            return false;
        }

        $domainParts = explode('.', $domain);

        if (count($domainParts) == 1) {
            return false;
        }

        $previousDomainParts = $domainParts;

        array_shift($previousDomainParts);
        array_unshift($previousDomainParts, '*');

        $wildcardDomain = implode('.', $previousDomainParts);

        return in_array($wildcardDomain, $tlds);
    }

    public static function getTLDs() {
        static $mozillaTlds = [];

        if (empty($mozillaTlds)) {
            $tldFile = CF::findFile('data', 'tldlist');
            $mozillaTlds = include $tldFile;
        }

        return $mozillaTlds;
    }
}
