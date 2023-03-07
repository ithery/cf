<?php

final class CServer_Domain {
    private static $whois;

    public static function whois() {
        return CServer_Domain_WhoIs::instance();
    }

    public static function getSSLInfo($domain) {
        $url = $domain;
        if (!cstr::startsWith($domain, 'https://')) {
            $url = 'https://' . $domain;
        }

        $checkSSL = new CServer_Domain_SslChecker([$url], 'U', 'Y-m-d H:i:s');

        return $checkSSL->add($url)->check();
    }

    public static function getTopLevelDomain($domain) {
        if (static::isTopLevelDomain($domain)) {
            return $domain;
        }
        $domainParts = explode('.', $domain);

        while (count($domainParts) > 2) {
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
         * Direct hit.
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
