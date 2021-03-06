<?php

namespace Spatie\Crawler;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class CrawlSubdomains extends CrawlProfile
{
    protected $baseUrl;

    public function __construct($baseUrl)
    {
        if (! $baseUrl instanceof UriInterface) {
            $baseUrl = new Uri($baseUrl);
        }

        $this->baseUrl = $baseUrl;
    }

    public function shouldCrawl(UriInterface $url)
    {
        return $this->isSubdomainOfHost($url);
    }

    public function isSubdomainOfHost(UriInterface $url)
    {
        return substr($url->getHost(), -strlen($this->baseUrl->getHost())) === $this->baseUrl->getHost();
    }
}
