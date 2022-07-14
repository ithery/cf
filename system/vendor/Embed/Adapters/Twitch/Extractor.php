<?php

namespace Embed\Adapters\Twitch;

use Embed\Extractor as Base;

class Extractor extends Base {
    /**
     * @return array
     */
    public function createCustomDetectors(): array {
        return [
            'code' => new Detectors\Code($this),
        ];
    }
}
