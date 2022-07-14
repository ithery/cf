<?php


namespace Embed\Adapters\Wikipedia\Detectors;

use Embed\Detectors\Description as Detector;

class Description extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->str('extract') ?: parent::detect();
    }

}
