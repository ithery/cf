<?php

namespace Embed\Adapters\Facebook;

use Embed\Extractor as Base;

class Extractor extends Base {
    public function createCustomDetectors() {
        $this->oembed = new OEmbed($this);

        return [
            'title' => new Detectors\Title($this),
        ];
    }
}
