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
}
