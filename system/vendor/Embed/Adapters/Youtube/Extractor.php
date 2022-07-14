<?php

//declare(strict_types = 1);

namespace Embed\Adapters\Youtube;

use Embed\Extractor as Base;

class Extractor extends Base {
    public function createCustomDetectors() {
        return [
            'feeds' => new Detectors\Feeds($this),
        ];
    }
}
