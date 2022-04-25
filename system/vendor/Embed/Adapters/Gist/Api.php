<?php

namespace Embed\Adapters\Gist;

use Embed\HttpApiTrait;

class Api {

    use HttpApiTrait;

    protected function fetchData() {
        $uri = $this->extractor->getUri();
        $this->endpoint = $uri->withPath($uri->getPath() . '.json');

        return $this->fetchJSON($this->endpoint);
    }

}
