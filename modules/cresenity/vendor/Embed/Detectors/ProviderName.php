<?php

//declare(strict_types = 1);

namespace Embed\Detectors;

class ProviderName extends Detector {

    private static $suffixes;

    public function detect() {
        $oembed = $this->extractor->getOEmbed();
        $metas = $this->extractor->getMetas();

        return $oembed->str('provider_name') ?: $metas->str('og:site_name') ?: ucfirst($this->fallback());
    }

    private function fallback() {
        $host = $this->extractor->getUri()->getHost();

        $host = array_reverse(explode('.', $host));

        switch (count($host)) {
            case 1:
                return $host[0];
            case 2:
                return $host[1];
            default:
                $tld = $host[1] . '.' . $host[0];
                $suffixes = self::getSuffixes();

                if (in_array($tld, $suffixes, true)) {
                    return $host[2];
                }

                return $host[1];
        }
    }

    private static function getSuffixes() {
        if (!isset(self::$suffixes)) {
            self::$suffixes = require dirname(__DIR__) . '/resources/suffix.php';
        }

        return self::$suffixes;
    }

}
