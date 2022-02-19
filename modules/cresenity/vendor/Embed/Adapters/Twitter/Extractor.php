<?php

namespace Embed\Adapters\Twitter;

use Embed\Extractor as Base;

class Extractor extends Base {
    private $api;

    public function getApi() {
        return $this->api;
    }

    public function createCustomDetectors() {
        $this->api = new Api($this);

        return [
            'authorName' => new Detectors\AuthorName($this),
            'authorUrl' => new Detectors\AuthorUrl($this),
            'description' => new Detectors\Description($this),
            'image' => new Detectors\Image($this),
            'providerName' => new Detectors\ProviderName($this),
            'publishedTime' => new Detectors\PublishedTime($this),
            'title' => new Detectors\Title($this),
        ];
    }
}
