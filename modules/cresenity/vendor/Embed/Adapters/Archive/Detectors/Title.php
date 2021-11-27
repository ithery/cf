<?php

namespace Embed\Adapters\Archive\Detectors;

use Embed\Detectors\Title as Detector;

class Title extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->str('metadata', 'title') ?: parent::detect();
    }

}
