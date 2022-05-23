<?php

namespace Embed\Adapters\Twitter\Detectors;

use Embed\Detectors\Title as Detector;

class Title extends Detector {
    /**
     * @return null|string
     */
    public function detect() {
        $extractor = $this->extractor;
        /** @var \Embed\Adapters\Twitter\Extractor $extractor */
        $api = $extractor->getApi();
        $name = $api->str('includes', 'users', '0', 'name');

        if ($name) {
            return "Tweet by ${name}";
        }

        return parent::detect();
    }
}
