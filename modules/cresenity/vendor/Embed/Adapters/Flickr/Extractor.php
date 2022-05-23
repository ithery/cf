<?php

namespace Embed\Adapters\Flickr;

use Embed\Extractor as Base;

class Extractor extends Base {
    /**
     * @return array
     */
    public function createCustomDetectors() {
        return [
            'code' => new Detectors\Code($this),
        ];
    }
}
