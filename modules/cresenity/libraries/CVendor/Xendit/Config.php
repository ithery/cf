<?php

class CVendor_Xendit_Config {
    public static $apiKey;

    public static $apiBase = 'https://api.xendit.co';

    public static $libVersion;

    private static $httpClient;

    const VERSION = '2.5.0';

    /**
     * ApiBase getter
     *
     * @return string
     */
    public static function getApiBase(): string {
        return self::$apiBase;
    }

    /**
     * ApiBase setter
     *
     * @param string $apiBase api base url
     *
     * @return void
     */
    public static function setApiBase(string $apiBase): void {
        self::$apiBase = $apiBase;
    }

    /**
     * Get the value of apiKey
     *
     * @return string Secret API key
     */
    public static function getApiKey() {
        return self::$apiKey;
    }

    /**
     * Set the value of apiKey
     *
     * @param string $apiKey Secret API key
     *
     * @return void
     */
    public static function setApiKey($apiKey) {
        self::$apiKey = $apiKey;
    }

    /**
     * Get library version
     *
     * @return mixed
     */
    public static function getLibVersion() {
        if (self::$libVersion === null) {
            self::$libVersion = self::VERSION;
        }
        return self::$libVersion;
    }

    /**
     * Set library version
     *
     * @param string $libVersion library version
     *
     * @return void
     */
    public static function setLibVersion($libVersion = null): void {
        self::$libVersion = $libVersion;
    }
}
