<?php

class CWebSocket_App {
    /**
     * @var string|int
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var null|string
     */
    public $name;

    /**
     * @var null|string
     */
    public $host;

    /**
     * @var null|string
     */
    public $path;

    /**
     * @var null|int
     */
    public $capacity = null;

    /**
     * @var bool
     */
    public $clientMessagesEnabled = false;

    /**
     * @var bool
     */
    public $statisticsEnabled = true;

    /**
     * @var array
     */
    public $allowedOrigins = [];

    /**
     * Find the app by id.
     *
     * @param string|int $appId
     *
     * @return null|\CWebSocket_App
     */
    public static function findById($appId) {
        return CWebSocket::appManager()->findById($appId);
    }

    /**
     * Find the app by app key.
     *
     * @param string $appKey
     *
     * @return null|\CWebSocket_App
     */
    public static function findByKey($appKey): ?self {
        return CWebSocket::appManager()->findByKey($appKey);
    }

    /**
     * Find the app by app secret.
     *
     * @param string $appSecret
     *
     * @return null|\CWebSocket_App
     */
    public static function findBySecret($appSecret): ?self {
        return CWebSocket::appManager()->findBySecret($appSecret);
    }

    /**
     * Initialize the Web Socket app instance.
     *
     * @param string|int $appId
     * @param string     $appKey
     * @param string     $appSecret
     *
     * @return void
     */
    public function __construct($appId, $appKey, $appSecret) {
        $this->id = $appId;
        $this->key = $appKey;
        $this->secret = $appSecret;
    }

    /**
     * Set the name of the app.
     *
     * @param string $appName
     *
     * @return $this
     */
    public function setName($appName) {
        $this->name = $appName;

        return $this;
    }

    /**
     * Set the app host.
     *
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host) {
        $this->host = $host;

        return $this;
    }

    /**
     * Set path for the app.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Enable client messages.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function enableClientMessages($enabled = true) {
        $this->clientMessagesEnabled = $enabled;

        return $this;
    }

    /**
     * Set the maximum capacity for the app.
     *
     * @param null|int $capacity
     *
     * @return $this
     */
    public function setCapacity($capacity) {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Enable statistics for the app.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function enableStatistics($enabled = true) {
        $this->statisticsEnabled = $enabled;

        return $this;
    }

    /**
     * Add whitelisted origins.
     *
     * @param array $allowedOrigins
     *
     * @return $this
     */
    public function setAllowedOrigins(array $allowedOrigins) {
        $this->allowedOrigins = $allowedOrigins;

        return $this;
    }
}
