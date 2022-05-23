<?php

namespace Embed\Adapters\Twitter\Detectors;

use Embed\Detectors\AuthorName as Detector;

class AuthorName extends Detector {
    /**
     * @return null|string
     */
    public function detect() {
        $extractor = $this->extractor;
        /** @var \Embed\Adapters\Twitter\Extractor $extractor */
        $api = $extractor->getApi();

        return $api->str('includes', 'users', '0', 'name')
            ?: parent::detect();
    }
}
