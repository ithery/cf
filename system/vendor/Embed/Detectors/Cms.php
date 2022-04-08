<?php

//declare(strict_types = 1);

namespace Embed\Detectors;

class Cms extends Detector {

    const BLOGSPOT = 'blogspot';
    const WORDPRESS = 'wordpress';
    const MEDIAWIKI = 'mediawiki';
    const OPENNEMAS = 'opennemas';

    public function detect() {
        $cms = self::detectFromHost($this->extractor->url->getHost());

        if ($cms) {
            return $cms;
        }

        $document = $this->extractor->getDocument();
        $generators = $document->select('.//meta', ['name' => 'generator'])->strAll('content');

        foreach ($generators as $generator) {
            if ($cms = self::detectFromGenerator($generator)) {
                return $cms;
            }
        }

        return null;
    }

    private static function detectFromHost($host) {
        if (strpos($host, '.blogspot.com') !== false) {
            return self::BLOGSPOT;
        }

        if (strpos($host, '.wordpress.com') !== false) {
            return self::WORDPRESS;
        }

        return null;
    }

    private static function detectFromGenerator($generator) {
        $generator = strtolower($generator);

        if ($generator === 'blogger') {
            return self::BLOGSPOT;
        }

        if (strpos($generator, 'mediawiki') === 0) {
            return self::MEDIAWIKI;
        }

        if (strpos($generator, 'wordpress') === 0) {
            return self::WORDPRESS;
        }

        if (strpos($generator, 'opennemas') === 0) {
            return self::OPENNEMAS;
        }

        return null;
    }

}
