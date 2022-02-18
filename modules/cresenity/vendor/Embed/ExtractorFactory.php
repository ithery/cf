<?php

//declare(strict_types=1);

namespace Embed;

use Embed\Http\Crawler;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ExtractorFactory {
    private $default = Extractor::class;

    private $adapters = [
        'slides.com' => Adapters\Slides\Extractor::class,
        'pinterest.com' => Adapters\Pinterest\Extractor::class,
        'flickr.com' => Adapters\Flickr\Extractor::class,
        'snipplr.com' => Adapters\Snipplr\Extractor::class,
        'play.cadenaser.com' => Adapters\CadenaSer\Extractor::class,
        'ideone.com' => Adapters\Ideone\Extractor::class,
        'gist.github.com' => Adapters\Gist\Extractor::class,
        'github.com' => Adapters\Github\Extractor::class,
        'wikipedia.org' => Adapters\Wikipedia\Extractor::class,
        'archive.org' => Adapters\Archive\Extractor::class,
        'sassmeister.com' => Adapters\Sassmeister\Extractor::class,
        'facebook.com' => Adapters\Facebook\Extractor::class,
        'instagram.com' => Adapters\Instagram\Extractor::class,
        'imageshack.com' => Adapters\ImageShack\Extractor::class,
        'youtube.com' => Adapters\Youtube\Extractor::class,
        'twitch.tv' => Adapters\Twitch\Extractor::class,
        'bandcamp.com' => Adapters\Bandcamp\Extractor::class,
    ];

    private $customDetectors = [];

    private $settings;

    public function __construct(array $settings = []) {
        if ($settings == null) {
            $settings = [];
        }
        $this->settings = $settings;
    }

    /**
     * @param UriInterface      $uri
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param Crawler           $crawler
     *
     * @return Extractor
     */
    public function createExtractor(UriInterface $uri, RequestInterface $request, ResponseInterface $response, Crawler $crawler) {
        $host = $uri->getHost();
        $class = $this->default;

        foreach ($this->adapters as $adapterHost => $adapter) {
            if (substr($host, -strlen($adapterHost)) === $adapterHost) {
                $class = $adapter;

                break;
            }
        }

        /** @var Extractor $extractor */
        $extractor = new $class($uri, $request, $response, $crawler);
        $extractor->setSettings($this->settings);

        foreach ($this->customDetectors as $name => $detector) {
            $extractor->addDetector($name, new $detector($extractor));
        }
        foreach ($extractor->createCustomDetectors() as $name => $detector) {
            $extractor->addDetector($name, $detector);
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

    public function setSettings($settings) {
        $this->settings = $settings;
    }
}
