<?php

namespace Embed\Adapters\Gist\Detectors;

use Datetime;
use Embed\Detectors\PublishedTime as Detector;

class PublishedTime extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->time('created_at') ?: parent::detect();
    }

}
