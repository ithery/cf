<?php

namespace Embed\Adapters\Twitter\Detectors;

use DateTime;
use Embed\Detectors\PublishedTime as Detector;

class PublishedTime extends Detector {
    /**
     * @return null|DateTime
     */
    public function detect() {
        $extractor = $this->extractor;
        /** @var \Embed\Adapters\Twitter\Extractor $extractor */
        $api = $extractor->getApi();

        return $api->time('data', 'created_at')
            ?: parent::detect();
    }
}
