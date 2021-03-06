<?php

//declare(strict_types = 1);

namespace Embed;

use DomainException;
use Embed\Http\Crawler;
use Embed\Detectors\Cms;
use Embed\Detectors\Url;
use Embed\Detectors\Code;
use Embed\Detectors\Icon;
use Embed\Detectors\Feeds;
use Embed\Detectors\Image;
use Embed\Detectors\Title;
use Embed\Detectors\Favicon;
use Embed\Detectors\License;
use Embed\Detectors\Detector;
use Embed\Detectors\Keywords;
use Embed\Detectors\Language;
use Embed\Detectors\Redirect;
use InvalidArgumentException;
use Embed\Detectors\AuthorUrl;
use Embed\Detectors\Languages;
use Embed\Detectors\AuthorName;
use Embed\Detectors\Description;
use Embed\Detectors\ProviderUrl;
use Embed\Detectors\ProviderName;
use Embed\Detectors\PublishedTime;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

require_once dirname(__FILE__) . '/functions.php';

/**
 * Class to extract the info.
 *
 * @property null|string          $authorName
 * @property null|UriInterface    $authorUrl
 * @property null|string          $cms
 * @property null|EmbedCode       $code
 * @property null|string          $description
 * @property UriInterface         $favicon
 * @property array|UriInterface[] $feeds
 * @property null|UriInterface    $icon
 * @property null|UriInterface    $image
 * @property array|string[]       $keywords
 * @property null|string          $language
 * @property array|UriInterface[] $languages
 * @property null|string          $license
 * @property string               $providerName
 * @property UriInterface         $providerUrl
 * @property null|DateTime        $publishedTime
 * @property null|UriInterface    $redirect
 * @property null|string          $title
 * @property UriInterface         $url
 */
class Extractor {
    /**
     * @var Document
     */
    protected $document;

    /**
     * @var OEmbed
     */
    protected $oembed;

    protected $linkedData;

    protected $metas;

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

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var Crawler
     */
    private $crawler;

    private $settings = [];

    private $customDetectors = [];

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
        $detector = isset($this->customDetectors[$name]) ? $this->customDetectors[$name] : (property_exists($this, $name) ? $this->$name : null);

        if (!$detector || !($detector instanceof Detector)) {
            throw new DomainException(sprintf('Invalid key "%s". No detector found for this value', $name));
        }

        return $detector->get();
    }

    public function createCustomDetectors() {
        return [];
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
