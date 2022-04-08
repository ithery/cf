<?php

namespace Embed\Adapters\Archive\Detectors;

use Embed\Detectors\AuthorName as Detector;

class AuthorName extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->str('metadata', 'creator') ?: parent::detect();
    }

}
