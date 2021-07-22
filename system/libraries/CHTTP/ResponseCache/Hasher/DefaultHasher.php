<?php

class CHTTP_ResponseCache_Hasher_DefaultHasher implements CHTTP_ResponseCache_Hasher_RequestHasherInterface {
    /**
     * CHTTP_ResponseCache_CacheProfile
     *
     * @var CHTTP_ResponseCache_CacheProfile
     */
    protected $cacheProfile;

    public function __construct(CHTTP_ResponseCache_CacheProfile $cacheProfile) {
        $this->cacheProfile = $cacheProfile;
    }

    public function getHashFor(CHTTP_Request $request) {
        $cacheNameSuffix = $this->getCacheNameSuffix($request);

        return 'responsecache-' . md5(
            "{$request->getHost()}-{$request->getRequestUri()}-{$request->getMethod()}/$cacheNameSuffix"
        );
    }

    protected function getCacheNameSuffix(CHTTP_Request $request) {
        if ($request->attributes->has('responsecache.cacheNameSuffix')) {
            return $request->attributes->get('responsecache.cacheNameSuffix');
        }

        return $this->cacheProfile->cacheNameSuffix($request);
    }
}
