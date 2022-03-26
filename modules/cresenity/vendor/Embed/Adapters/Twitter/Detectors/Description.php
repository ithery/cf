<?php

namespace Embed\Adapters\Twitter\Detectors;

use Embed\Detectors\Description as Detector;

class Description extends Detector {
    /**
     * @return null|string
     */
    public function detect() {
        $extractor = $this->extractor;
        /** @var \Embed\Adapters\Twitter\Extractor $extractor */
        $api = $extractor->getApi();

        return $api->str('data', 'text')
            ?: parent::detect();
    }
}
