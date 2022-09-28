<?php

namespace Embed\Adapters\Gist\Detectors;

use Embed\Detectors\AuthorName as Detector;

class AuthorName extends Detector {

    public function detect() {
        $api = $this->extractor->getApi();

        return $api->str('owner') ?: parent::detect();
    }

}
