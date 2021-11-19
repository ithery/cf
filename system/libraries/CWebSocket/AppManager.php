<?php

class CWebSocket_AppManager implements CWebSocket_AppManager {
    /**
     * The list of apps.
     *
     * @var \CCollection
     */
    protected $apps;

    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Initialize the class.
     *
     * @return void
     */
    public function __construct() {
        $this->apps = c::collect(CF::config('websocket.apps'));
    }

    /**
     * Get all apps.
     *
     * @return array[\CWebSocket_App]
     */
    public function all() {
        return $this->apps
            ->map(function (array $appAttributes) {
                return $this->convertIntoApp($appAttributes);
            })
            ->toArray();
    }

    /**
     * Get app by id.
     *
     * @param string|int $appId
     *
     * @return null|\CWebSocket_App
     */
    public function findById($appId) {
        return $this->convertIntoApp(
            $this->apps->firstWhere('id', $appId)
        );
    }

    /**
     * Get app by app key.
     *
     * @param string $appKey
     *
     * @return null|\CWebSocket_App
     */
    public function findByKey($appKey) {
        return $this->convertIntoApp(
            $this->apps->firstWhere('key', $appKey)
        );
    }

    /**
     * Get app by secret.
     *
     * @param string $appSecret
     *
     * @return null|\CWebSocket_App
     */
    public function findBySecret($appSecret) {
        return $this->convertIntoApp(
            $this->apps->firstWhere('secret', $appSecret)
        );
    }

    /**
     * Map the app into an App instance.
     *
     * @param null|array $appAttributes
     *
     * @return null|\CWebSocket_App
     */
    protected function convertIntoApp($appAttributes) {
        if (!$appAttributes) {
            return null;
        }

        $app = new CWebSocket_App(
            $appAttributes['id'],
            $appAttributes['key'],
            $appAttributes['secret']
        );

        if (isset($appAttributes['name'])) {
            $app->setName($appAttributes['name']);
        }

        if (isset($appAttributes['host'])) {
            $app->setHost($appAttributes['host']);
        }

        if (isset($appAttributes['path'])) {
            $app->setPath($appAttributes['path']);
        }

        $app
            ->enableClientMessages($appAttributes['enable_client_messages'])
            ->enableStatistics($appAttributes['enable_statistics'])
            ->setCapacity(isset($appAttributes['capacity']) ? $appAttributes['capacity'] : null)
            ->setAllowedOrigins(isset($appAttributes['allowed_origins']) ? $appAttributes['allowed_origins'] : []);

        return $app;
    }
}
