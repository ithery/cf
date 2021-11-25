<?php

//declare(strict_types = 1);

namespace Embed\Detectors;

use Psr\Http\Message\UriInterface;

class AuthorUrl extends Detector {

    public function detect() {
        $oembed = $this->extractor->getOEmbed();

        return $oembed->url('author_url') ?: $this->detectFromTwitter();
    }

    private function detectFromTwitter() {
        $metas = $this->extractor->getMetas();
        $crawler = $this->extractor->getCrawler();

        $user = $metas->str('twitter:creator');

        return $user ? $crawler->createUri(sprintf('https://twitter.com/%s', ltrim($user, '@'))) : null;
    }

}
