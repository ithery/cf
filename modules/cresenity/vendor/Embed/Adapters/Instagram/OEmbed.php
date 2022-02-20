<?php

namespace Embed\Adapters\Instagram;

use Embed\OEmbed as Base;
use Psr\Http\Message\UriInterface;

class OEmbed extends Base {
    const ENDPOINT = 'https://graph.facebook.com/v8.0/instagram_oembed';

    /**
     * @return null|UriInterface
     */
    protected function detectEndpoint() {
        $token = $this->extractor->getSetting('instagram:token');

        if (!$token) {
            return null;
        }

        $uri = $this->extractor->getUri();
        $queryParameters = $this->getOembedQueryParameters((string) $uri);
        $queryParameters['access_token'] = $token;

        return $this->extractor->getCrawler()
            ->createUri(self::ENDPOINT)
            ->withQuery(http_build_query($queryParameters));
    }
}
