<?php

namespace Embed\Adapters\Twitter\Detectors;

use Psr\Http\Message\UriInterface;
use Embed\Detectors\AuthorUrl as Detector;

class AuthorUrl extends Detector {
    /**
     * @return null|UriInterface
     */
    public function detect() {
        $extractor = $this->extractor;
        /** @var \Embed\Adapters\Twitter\Extractor $extractor */
        $api = $extractor->getApi();
        $username = $api->str('includes', 'users', '0', 'username');

        if ($username) {
            return $extractor->getCrawler()->createUri("https://twitter.com/{$username}");
        }

        return parent::detect();
    }
}
