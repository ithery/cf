<?php

final class CVendor_Firebase_JWT_Action_FetchGooglePublicKeys {
    public const DEFAULT_URLS = [
        'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com',
        'https://www.googleapis.com/oauth2/v1/certs',
        'https://www.googleapis.com/identitytoolkit/v3/relyingparty/publicKeys',
    ];

    public const DEFAULT_FALLBACK_CACHE_DURATION = 'PT1H';

    /**
     * @var array<int, string>
     */
    private array $urls;

    /**
     * @var CVendor_Firebase_JWT_Value_Duration
     */
    private $fallbackCacheDuration;

    /**
     * @param array<array-key, string> $urls
     */
    private function __construct(array $urls, CVendor_Firebase_JWT_Value_Duration $fallbackCacheDuration) {
        $this->urls = \array_values($urls);
        $this->fallbackCacheDuration = $fallbackCacheDuration;
    }

    /**
     * @return self
     */
    public static function fromGoogle() {
        return new self(self::DEFAULT_URLS, CVendor_Firebase_JWT_Value_Duration::fromDateIntervalSpec(self::DEFAULT_FALLBACK_CACHE_DURATION));
    }

    /**
     * Use this method only if Google has changed the default URL and the library hasn't been updated yet.
     *
     * @param string $url
     *
     * @return self
     */
    public static function fromUrl($url) {
        return new self([$url], CVendor_Firebase_JWT_Value_Duration::fromDateIntervalSpec(self::DEFAULT_FALLBACK_CACHE_DURATION));
    }

    /**
     * A response from the Google APIs should have a cache control header that determines when the keys expire.
     * If it doesn't have one, fall back to this value.
     *
     * @param CVendor_Firebase_JWT_Value_Duration|DateInterval|string|int $duration
     *
     * @return self
     */
    public function ifKeysDoNotExpireCacheFor($duration) {
        $duration = CVendor_Firebase_JWT_Value_Duration::make($duration);

        $action = clone $this;
        $action->fallbackCacheDuration = $duration;

        return $action;
    }

    /**
     * @return array<int, string>
     */
    public function urls() {
        return $this->urls;
    }

    /**
     * @return CVendor_Firebase_JWT_Value_Duration
     */
    public function getFallbackCacheDuration() {
        return $this->fallbackCacheDuration;
    }
}
