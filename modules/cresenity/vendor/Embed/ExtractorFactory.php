<?php

//declare(strict_types=1);

namespace Embed;

use Embed\Http\Crawler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class ExtractorFactory {

    private $default = Extractor::class;
    private $adapters = [
        'slides.com' => Adapters\Slides\Extractor::class,
        'pinterest.com' => Adapters\Pinterest\Extractor::class,
        'flickr.com' => Adapters\Flickr\Extractor::class,
        'snipplr.com' => Adapters\Snipplr\Extractor::class,
        'play.cadenaser.com' => Adapters\CadenaSer\Extractor::class,
        'ideone.com' => Adapters\Ideone\Extractor::class,
        'github.com' => Adapters\Github\Extractor::class,
        'gist.github.com' => Adapters\Gist\Extractor::class,
        'en.wikipedia.org' => Adapters\Wikipedia\Extractor::class,
        'es.wikipedia.org' => Adapters\Wikipedia\Extractor::class,
        'gl.wikipedia.org' => Adapters\Wikipedia\Extractor::class,
        'archive.org' => Adapters\Archive\Extractor::class,
        'sassmeister.com' => Adapters\Sassmeister\Extractor::class,
        'facebook.com' => Adapters\Facebook\Extractor::class,
        'imageshack.com' => Adapters\ImageShack\Extractor::class,
        'imagizer.imageshack.com' => Adapters\ImageShack\Extractor::class,
        'youtube.com' => Adapters\Youtube\Extractor::class,
        'twitch.tv' => Adapters\Twitch\Extractor::class,
    ];
    private $customDetectors = [];

    public function createExtractor(UriInterface $uri, RequestInterface $request, ResponseInterface $response, Crawler $crawler) {
        $host = $uri->getHost();
        $host = str_replace('www.', '', $host);

        $class = isset($this->adapters[$host]) ? $this->adapters[$host] : $this->default;

        $extractor = new $class($uri, $request, $response, $crawler);

        foreach ($this->customDetectors as $name => $detector) {
            $extractor->addDetector($name, new $detector($extractor));
        }

        return $extractor;
    }

    public function addAdapter($pattern, $class) {
        $this->adapters[$pattern] = $class;
    }

    public function addDetector($name, $class) {
        $this->customDetectors[$name] = $class;
    }

    public function removeAdapter($pattern) {
        unset($this->adapters[$pattern]);
    }

    public function setDefault($class) {
        $this->default = $class;
    }

}
