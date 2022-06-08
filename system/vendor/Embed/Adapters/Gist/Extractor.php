<?php

namespace Embed\Adapters\Gist;

use Embed\Http\Crawler;
use Embed\Extractor as Base;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Extractor extends Base {
    private $api;

    /**
     * @return array
     */
    public function createCustomDetectors() {
        $this->api = new Api($this);

        return [
            'authorName' => new Detectors\AuthorName($this),
            'authorUrl' => new Detectors\AuthorUrl($this),
            'publishedTime' => new Detectors\PublishedTime($this),
            'code' => new Detectors\Code($this),
        ];
    }

    /**
     * @return Api
     */
    public function getApi() {
        return $this->api;
    }
}
