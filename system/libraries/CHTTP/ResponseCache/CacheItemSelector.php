<?php

class CHTTP_ResponseCache_CacheItemSelector {
    protected $method = 'GET';

    protected $parameters = [];

    protected $cookies = [];

    protected $server = [];

    protected $cacheNameSuffix = null;

    protected $hasher;

    protected $cache;

    protected $urls = [];

    protected $tags = [];

    public function __construct(CHTTP_ResponseCache_Hasher_RequestHasherInterface $hasher, CHTTP_ResponseCache_Repository $cache) {
        $this->hasher = $hasher;
        $this->cache = $cache;
    }

    /**
     * @param string | array $tags
     *
     * @return static
     */
    public function usingTags($tags) {
        $this->tags = is_array($tags) ? $tags : func_get_args();

        return $this;
    }

    /**
     * @param string | array $urls
     *
     * @return static
     */
    public function forUrls($urls) {
        $this->urls = is_array($urls) ? $urls : func_get_args();

        return $this;
    }

    /**
     * @return void
     */
    public function forget() {
        c::collect($this->urls)
            ->map(function ($uri) {
                $request = $this->build($uri);

                return $this->hasher->getHashFor($request);
            })
            ->filter(function ($hash) {
                return $this->taggedCache($this->tags)->has($hash);
            })
            ->each(function ($hash) {
                return $this->taggedCache($this->tags)->forget($hash);
            });
    }

    /**
     * @param array $tags
     *
     * @return CHTTP_ResponseCache_Repository
     */
    protected function taggedCache(array $tags = []) {
        return empty($tags)
            ? $this->cache
            : $this->cache->tags($tags);
    }

    public function withPutMethod() {
        $this->method = 'PUT';

        return $this;
    }

    public function withPatchMethod() {
        $this->method = 'PATCH';

        return $this;
    }

    public function withPostMethod() {
        $this->method = 'POST';

        return $this;
    }

    /**
     * If method is GET then will be converted to query
     * otherwise it will became part of request input
     *
     * @param array $parameters
     */
    public function withParameters($parameters) {
        $this->parameters = $parameters;

        return $this;
    }

    public function withCookies($cookies) {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * WithHeaders
     *
     * @param array $headers
     *
     * @return self
     */
    public function withHeaders(array $headers) {
        $this->server = c::collect($this->server)
            ->filter(function ($val, $key) {
                return !cstr::startsWith($key, 'HTTP_');
            })
            ->merge(c::collect($headers)
                ->mapWithKeys(function ($val, $key) {
                    return ['HTTP_' . str_replace('-', '_', cstr::upper($key)) => $val];
                }))
            ->toArray();

        return $this;
    }

    public function withRemoteAddress($remoteAddress) {
        $this->server['REMOTE_ADDR'] = $remoteAddress;

        return $this;
    }

    public function usingSuffix($cacheNameSuffix) {
        $this->cacheNameSuffix = $cacheNameSuffix;

        return $this;
    }

    /**
     * @param string $uri
     *
     * @return CHTTP_Request
     */
    protected function build($uri) {
        $request = CHTTP_Request::create(
            $uri,
            $this->method,
            $this->parameters,
            $this->cookies,
            [],
            $this->server
        );

        if (isset($this->cacheNameSuffix)) {
            $request->attributes->add([
                'responsecache.cacheNameSuffix' => $this->cacheNameSuffix,
            ]);
        }

        return $request;
    }
}
