<?php

namespace Embed\Adapters\Archive\Detectors;

use Embed\Detectors\Description as Detector;

class Description extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->str('metadata', 'extract') ?: parent::detect();
    }

}
