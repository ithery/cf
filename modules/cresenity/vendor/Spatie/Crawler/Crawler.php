<?php

namespace Spatie\Crawler;

use Generator;
use Tree\Node\Node;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Request;
use Spatie\Robots\RobotsTxt;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\CrawlQueue\CrawlQueue;
use Spatie\Crawler\Handlers\CrawlRequestFailed;
use Spatie\Crawler\Handlers\CrawlRequestFulfilled;
use Spatie\Crawler\CrawlQueue\CollectionCrawlQueue;
use Spatie\Crawler\Exception\InvalidCrawlRequestHandler;

class Crawler {

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Psr\Http\Message\UriInterface */
    protected $baseUrl;

    /** @var \Spatie\Crawler\CrawlObserverCollection */
    protected $crawlObservers;

    /** @var \Spatie\Crawler\CrawlProfile */
    protected $crawlProfile;

    /** @var int */
    protected $concurrency;

    /** @var \Spatie\Crawler\CrawlQueue\CrawlQueue */
    protected $crawlQueue;

    /** @var int */
    protected $crawledUrlCount = 0;

    /** @var int|null */
    protected $maximumCrawlCount = null;

    /** @var int */
    protected $maximumResponseSize = 1024 * 1024 * 2;

    /** @var int|null */
    protected $maximumDepth = null;

    /** @var bool */
    protected $respectRobots = true;

    /** @var \Tree\Node\Node */
    protected $depthTree;

    /** @var bool */
    protected $executeJavaScript = false;

    /** @var Browsershot */
    protected $browsershot = null;

    /** @var \Spatie\Robots\RobotsTxt */
    protected $robotsTxt = null;

    /** @var string */
    protected $crawlRequestFulfilledClass;

    /** @var string */
    protected $crawlRequestFailedClass;

    /** @var float */
    protected $delayBetweenRequests = 0;

    /** @var   */
    protected static $defaultClientOptions = [
        RequestOptions::COOKIES => true,
        RequestOptions::CONNECT_TIMEOUT => 10,
        RequestOptions::TIMEOUT => 10,
        RequestOptions::ALLOW_REDIRECTS => false,
    ];

    public static function create(array $clientOptions = []) {
        $clientOptions = (count($clientOptions)) ? $clientOptions : static::$defaultClientOptions;

        $client = new Client($clientOptions);

        return new static($client);
    }

    public function __construct(Client $client, $concurrency = 10) {
        $this->client = $client;

        $this->concurrency = $concurrency;

        $this->crawlProfile = new CrawlAllUrls();

        $this->crawlQueue = new CollectionCrawlQueue();

        $this->crawlObservers = new CrawlObserverCollection();

        $this->crawlRequestFulfilledClass = CrawlRequestFulfilled::class;

        $this->crawlRequestFailedClass = CrawlRequestFailed::class;
    }

    public function setConcurrency($concurrency) {
        $this->concurrency = $concurrency;

        return $this;
    }

    public function setMaximumResponseSize($maximumResponseSizeInBytes) {
        $this->maximumResponseSize = $maximumResponseSizeInBytes;

        return $this;
    }

    public function getMaximumResponseSize() {
        return $this->maximumResponseSize;
    }

    public function setMaximumCrawlCount($maximumCrawlCount) {
        $this->maximumCrawlCount = $maximumCrawlCount;

        return $this;
    }

    public function getMaximumCrawlCount() {
        return $this->maximumCrawlCount;
    }

    public function getCrawlerUrlCount() {
        return $this->crawledUrlCount;
    }

    public function setMaximumDepth($maximumDepth) {
        $this->maximumDepth = $maximumDepth;

        return $this;
    }

    public function getMaximumDepth() {
        return $this->maximumDepth;
    }

    public function setDelayBetweenRequests($delay) {
        $this->delayBetweenRequests = ($delay * 1000);

        return $this;
    }

    public function getDelayBetweenRequests() {
        return $this->delayBetweenRequests;
    }

    public function ignoreRobots() {
        $this->respectRobots = false;

        return $this;
    }

    public function respectRobots() {
        $this->respectRobots = true;

        return $this;
    }

    public function mustRespectRobots() {
        return $this->respectRobots;
    }

    public function getRobotsTxt() {
        return $this->robotsTxt;
    }

    public function setCrawlQueue(CrawlQueue $crawlQueue) {
        $this->crawlQueue = $crawlQueue;

        return $this;
    }

    public function getCrawlQueue() {
        return $this->crawlQueue;
    }

    public function executeJavaScript() {
        $this->executeJavaScript = true;

        return $this;
    }

    public function doNotExecuteJavaScript() {
        $this->executeJavaScript = false;

        return $this;
    }

    public function mayExecuteJavascript() {
        return $this->executeJavaScript;
    }

    /**
     * @param \Spatie\Crawler\CrawlObserver|array[\Spatie\Crawler\CrawlObserver] $crawlObservers
     *
     * @return $this
     */
    public function setCrawlObserver($crawlObservers) {
        if (!is_array($crawlObservers)) {
            $crawlObservers = [$crawlObservers];
        }

        return $this->setCrawlObservers($crawlObservers);
    }

    public function setCrawlObservers(array $crawlObservers) {
        $this->crawlObservers = new CrawlObserverCollection($crawlObservers);

        return $this;
    }

    public function addCrawlObserver(CrawlObserver $crawlObserver) {
        $this->crawlObservers->addObserver($crawlObserver);

        return $this;
    }

    public function getCrawlObservers() {
        return $this->crawlObservers;
    }

    public function setCrawlProfile(CrawlProfile $crawlProfile) {
        $this->crawlProfile = $crawlProfile;

        return $this;
    }

    public function getCrawlProfile() {
        return $this->crawlProfile;
    }

    public function setCrawlFulfilledHandlerClass($crawlRequestFulfilledClass) {
        $baseClass = CrawlRequestFulfilled::class;

        if (!is_subclass_of($crawlRequestFulfilledClass, $baseClass)) {
            throw InvalidCrawlRequestHandler::doesNotExtendBaseClass($crawlRequestFulfilledClass, $baseClass);
        }

        $this->crawlRequestFulfilledClass = $crawlRequestFulfilledClass;

        return $this;
    }

    public function setCrawlFailedHandlerClass($crawlRequestFailedClass) {
        $baseClass = CrawlRequestFailed::class;

        if (!is_subclass_of($crawlRequestFailedClass, $baseClass)) {
            throw InvalidCrawlRequestHandler::doesNotExtendBaseClass($crawlRequestFailedClass, $baseClass);
        }

        $this->crawlRequestFailedClass = $crawlRequestFailedClass;

        return $this;
    }

    public function setBrowsershot(Browsershot $browsershot) {
        $this->browsershot = $browsershot;

        return $this;
    }

    public function getBrowsershot() {
        if (!$this->browsershot) {
            $this->browsershot = new Browsershot();
        }

        return $this->browsershot;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * @param \Psr\Http\Message\UriInterface|string $baseUrl
     */
    public function startCrawling($baseUrl) {
        if (!$baseUrl instanceof UriInterface) {
            $baseUrl = new Uri($baseUrl);
        }

        if ($baseUrl->getScheme() === '') {
            $baseUrl = $baseUrl->withScheme('http');
        }

        if ($baseUrl->getPath() === '') {
            $baseUrl = $baseUrl->withPath('/');
        }

        $this->baseUrl = $baseUrl;

        $crawlUrl = CrawlUrl::create($this->baseUrl);

        $this->robotsTxt = $this->createRobotsTxt($crawlUrl->url);

        if ($this->robotsTxt->allows((string) $crawlUrl->url) ||
                !$this->respectRobots
        ) {
            $this->addToCrawlQueue($crawlUrl);
        }

        $this->depthTree = new Node((string) $this->baseUrl);

        $this->startCrawlingQueue();

        foreach ($this->crawlObservers as $crawlObserver) {
            $crawlObserver->finishedCrawling();
        }
    }

    public function addToDepthTree(UriInterface $url, UriInterface $parentUrl, Node $node = null) {
        if (is_null($this->maximumDepth)) {
            return new Node((string) $url);
        }

        $node = $node == null ? $this->depthTree : $node;

        $returnNode = null;

        if ($node->getValue() === (string) $parentUrl) {
            $newNode = new Node((string) $url);

            $node->addChild($newNode);

            return $newNode;
        }

        foreach ($node->getChildren() as $currentNode) {
            $returnNode = $this->addToDepthTree($url, $parentUrl, $currentNode);

            if (!is_null($returnNode)) {
                break;
            }
        }

        return $returnNode;
    }

    protected function startCrawlingQueue() {
        while ($this->crawlQueue->hasPendingUrls()) {
            $pool = new Pool($this->client, $this->getCrawlRequests(), [
                'concurrency' => $this->concurrency,
                'options' => $this->client->getConfig(),
                'fulfilled' => new $this->crawlRequestFulfilledClass($this),
                'rejected' => new $this->crawlRequestFailedClass($this),
            ]);

            $promise = $pool->promise();

            $promise->wait();
        }
    }

    /**
     * @deprecated This function will be removed in the next major version
     */
    public function endsWith($haystack, $needle) {
        return strrpos($haystack, $needle) + strlen($needle) === strlen($haystack);
    }

    protected function createRobotsTxt(UriInterface $uri) {
        return RobotsTxt::create($uri->withPath('/robots.txt'));
    }

    protected function getCrawlRequests() {
        while ($crawlUrl = $this->crawlQueue->getFirstPendingUrl()) {
            if (!$this->crawlProfile->shouldCrawl($crawlUrl->url)) {
                $this->crawlQueue->markAsProcessed($crawlUrl);
                continue;
            }

            if ($this->crawlQueue->hasAlreadyBeenProcessed($crawlUrl)) {
                continue;
            }

            foreach ($this->crawlObservers as $crawlObserver) {
                $crawlObserver->willCrawl($crawlUrl->url);
            }

            $this->crawlQueue->markAsProcessed($crawlUrl);

            yield $crawlUrl->getId() => new Request('GET', $crawlUrl->url);
        }
    }

    public function addToCrawlQueue(CrawlUrl $crawlUrl) {
        if (!$this->getCrawlProfile()->shouldCrawl($crawlUrl->url)) {
            return $this;
        }

        if ($this->getCrawlQueue()->has($crawlUrl->url)) {
            return $this;
        }

        $this->crawledUrlCount++;

        $this->crawlQueue->add($crawlUrl);

        return $this;
    }

    public function maximumCrawlCountReached() {
        $maximumCrawlCount = $this->getMaximumCrawlCount();

        if (is_null($maximumCrawlCount)) {
            return false;
        }

        return $this->getCrawlerUrlCount() >= $maximumCrawlCount;
    }

}
