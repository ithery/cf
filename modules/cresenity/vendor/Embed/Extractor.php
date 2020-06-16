<?php

//declare(strict_types = 1);

namespace Embed;

use DomainException;
use Embed\Detectors\AuthorName;
use Embed\Detectors\AuthorUrl;
use Embed\Detectors\Cms;
use Embed\Detectors\Code;
use Embed\Detectors\Description;
use Embed\Detectors\Detector;
use Embed\Detectors\Favicon;
use Embed\Detectors\Feeds;
use Embed\Detectors\Icon;
use Embed\Detectors\Image;
use Embed\Detectors\Keywords;
use Embed\Detectors\Language;
use Embed\Detectors\Languages;
use Embed\Detectors\License;
use Embed\Detectors\ProviderName;
use Embed\Detectors\ProviderUrl;
use Embed\Detectors\PublishedTime;
use Embed\Detectors\Redirect;
use Embed\Detectors\Title;
use Embed\Detectors\Url;
use Embed\Http\Crawler;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;



require_once dirname(__FILE__) . '/functions.php';

/**
 * Class to extract the info
 */
class Extractor {

    private $request;
    private $response;
    private $uri;
    private $crawler;
    private $document;
    private $oembed;
    private $linkedData;
    private $metas;
    private $settings = [];
    private $customDetectors = [];
    protected $authorName;
    protected $authorUrl;
    protected $cms;
    protected $code;
    protected $description;
    protected $favicon;
    protected $feeds;
    protected $icon;
    protected $image;
    protected $keywords;
    protected $language;
    protected $languages;
    protected $license;
    protected $providerName;
    protected $providerUrl;
    protected $publishedTime;
    protected $redirect;
    protected $title;
    protected $url;

    public function __construct(UriInterface $uri, RequestInterface $request, ResponseInterface $response, Crawler $crawler) {
        $this->uri = $uri;
        $this->request = $request;
        $this->response = $response;
        $this->crawler = $crawler;

        //APIs
        $this->document = new Document($this);
        $this->oembed = new OEmbed($this);
        $this->linkedData = new LinkedData($this);
        $this->metas = new Metas($this);

        //Detectors
        $this->authorName = new AuthorName($this);
        $this->authorUrl = new AuthorUrl($this);
        $this->cms = new Cms($this);
        $this->code = new Code($this);
        $this->description = new Description($this);
        $this->favicon = new Favicon($this);
        $this->feeds = new Feeds($this);
        $this->icon = new Icon($this);
        $this->image = new Image($this);
        $this->keywords = new Keywords($this);
        $this->language = new Language($this);
        $this->languages = new Languages($this);
        $this->license = new License($this);
        $this->providerName = new ProviderName($this);
        $this->providerUrl = new ProviderUrl($this);
        $this->publishedTime = new PublishedTime($this);
        $this->redirect = new Redirect($this);
        $this->title = new Title($this);
        $this->url = new Url($this);
    }

    public function __get($name) {
        $detector = isset($this->customDetectors[$name]) ? $this->customDetectors[$name] : property_exists($this, $name) ? $this->$name : null;

        if (!$detector || !($detector instanceof Detector)) {
            throw new DomainException(sprintf('Invalid key "%s". No detector found for this value', $name));
        }

        return $detector->get();
    }

    public function addDetector($name, Detector $detector) {
        $this->customDetectors[$name] = $detector;
    }

    public function setSettings(array $settings) {
        $this->settings = $settings;
    }

    public function getSettings() {
        return $this->settings;
    }

    public function getSetting($key) {
        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    public function getDocument() {
        return $this->document;
    }

    public function getOEmbed() {
        return $this->oembed;
    }

    public function getLinkedData() {
        return $this->linkedData;
    }

    public function getMetas() {
        return $this->metas;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getUri() {
        return $this->uri;
    }

    /**
     * @param UriInterface|string $uri
     */
    public function resolveUri($uri) {
        if (is_string($uri)) {
            if (!isHttp($uri)) {
                throw new InvalidArgumentException(sprintf('Uri string must use http or https scheme (%s)', $uri));
            }

            $uri = $this->crawler->createUri($uri);
        }

        if (!($uri instanceof UriInterface)) {
            throw new InvalidArgumentException('Uri must be a string or an instance of UriInterface');
        }

        return resolveUri($this->uri, $uri);
    }

    public function getCrawler() {
        return $this->crawler;
    }

}
