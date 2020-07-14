<?php

namespace Embed\Adapters\Archive\Detectors;

use Datetime;
use Embed\Detectors\PublishedTime as Detector;

class PublishedTime extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->time('metadata', 'publicdate') ?: $api->time('metadata', 'addeddate') ?: $api->time('metadata', 'date') ?: parent::detect();
    }

}
