<?php

use Symfony\Component\HttpFoundation\Response;

class CHTTP_ResponseCache_Repository {
    protected $responseSerializer;

    protected $cache;

    public function __construct($cache = null) {
        $this->responseSerializer = new CHTTP_ResponseCache_Serializer_DefaultSerializer();
        $this->cache = $cache;
    }

    /**
     * @param string        $key
     * @param Response      $response
     * @param \DateTime|int $seconds
     *
     * @return void
     */
    public function put($key, Response $response, $seconds) {
        if ($this->cache != null) {
            $this->cache->put($key, $this->responseSerializer->serialize($response), is_numeric($seconds) ? c::now()->addSeconds($seconds) : $seconds);
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key) {
        if ($this->cache != null) {
            return $this->cache->has($key);
        }
        return false;
    }

    /**
     * @param string $key
     *
     * @return Response
     */
    public function get($key) {
        if ($this->cache != null) {
            return $this->responseSerializer->unserialize($this->cache->get($key));
        }
        return null;
    }

    /**
     * @return void
     */
    public function clear() {
        if ($this->cache != null) {
            if ($this->cache instanceof CCache_TaggedCache && !empty($this->cache->getTags())) {
                $this->cache->flush();

                return;
            }
            $this->cache->clear();
        }
        // $cacheTag = CHTTP::responseCache()->cacheProfile()->cacheTag();

        // if (empty($cacheTag) {
        //     $this->cache->clear();

        //     return;
        // }

        // $this->cache->tags($cacheTag)->flush();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function forget($key) {
        if ($this->cache != null) {
            return $this->cache->forget($key);
        }
    }

    /**
     * @param array $tags
     *
     * @return self
     */
    public function tags(array $tags) {
        if ($this->cache != null) {
            if ($this->cache instanceof CCache_TaggedCache && !empty($this->cache->getTags())) {
                $tags = array_merge($this->cache->getTags()->getNames(), $tags);
            }

            return new self($this->cache->tags($tags));
        }
    }

    /**
     * @param mixed $repository
     *
     * @return bool
     */
    public function isTagged($repository) {
        return $repository instanceof CCache_TaggedCache && !empty($repository->getTags());
    }

    public function setCache(CCache_Repository $cache) {
        $this->cache = $cache;
        return $this;
    }

    public function hasCache() {
        return $this->cache != null;
    }
}
