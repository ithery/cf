<?php

namespace Embed\Adapters\Wikipedia\Detectors;

use Embed\Detectors\Title as Detector;

class Title extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->str('title') ?: parent::detect();
    }

}
