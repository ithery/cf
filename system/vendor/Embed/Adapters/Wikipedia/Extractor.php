<?php

namespace Embed\Adapters\Wikipedia;

use Embed\Extractor as Base;

class Extractor extends Base {
    private $api;

    public function getApi() {
        return $this->api;
    }

    public function createCustomDetectors() {
        $this->api = new Api($this);

        return [
            'title' => new Detectors\Title($this),
            'description' => new Detectors\Description($this),
        ];
    }
}
