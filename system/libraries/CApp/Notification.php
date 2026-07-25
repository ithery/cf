<?php
/**
 * @see CApp
 */
class CApp_Notification {
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var null|string
     */
    protected $driver;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $startUrl;

    /**
     * @var string
     */
    protected $sendTokenPath;

    /**
     * @var string
     */
    protected $tokenLocalStorageKey;

    /**
     * @var null|string
     */
    protected $group;

    /**
     * @var CApp_Notification
     */
    private static $instance;

    /**
     * @return CApp_Notification
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * CApp_Notification constructor.
     */
    private function __construct() {
        $this->group = null;
        $this->config = CF::config('notification.web');
        $this->debug = carr::get($this->config, 'debug', false);
        $this->driver = carr::get($this->config, 'driver');
        $this->startUrl = carr::get($this->config, 'start_url', '');
        $this->sendTokenPath = carr::get($this->config, 'sendTokenPath', 'notification/token');
        $this->tokenLocalStorageKey = carr::get($this->config, 'tokenLocalStorageKey', 'cres-' . $this->driver . '-token' . ($this->group ? '-' . $this->group : ''));
        $options = carr::get($this->config, 'options', []);
        if (is_string($options)) {
            $options = json_decode($options, true);
        }
        $this->options = $options;
    }

    /**
     * @param null|string $group
     *
     * @return void
     */
    public function enable($group = null) {
        if (!$this->enabled) {
            $this->group = $group;
            $path = c::request()->path();
            if ($this->group) {
                $groupConfig = CF::config('notification.web.groups.' . $this->group);
                $this->config = array_merge($this->config, $groupConfig);
                $this->debug = carr::get($this->config, 'debug', false);
                $this->driver = carr::get($this->config, 'driver');
                $this->startUrl = carr::get($this->config, 'start_url', '');
                $this->sendTokenPath = carr::get($this->config, 'sendTokenPath', 'notification/token');
                $this->tokenLocalStorageKey = carr::get($this->config, 'tokenLocalStorageKey', 'cres-' . $this->driver . '-token' . ($this->group ? '-' . $this->group : ''));
            }
            if (isset($this->config['groups'])) {
                unset($this->config['groups']);
            }
            if (cstr::startsWith($path, trim($this->startUrl, '/'))) {
                c::router()->get($this->serviceWorkerUrl(), function () {
                    //disable debug bar here
                    if (CDebug::bar()->isEnabled()) {
                        CDebug::bar()->disable();
                    }
                    $options = [
                        'driver' => $this->driver,
                        'options' => $this->options,

                    ];
                    $output = (new CApp_Notification_ServiceWorkerService())->generate($options);

                    return c::response($output, 200, [
                        'Content-Type' => 'text/javascript',
                    ]);
                });
                $this->enabled = true;
            }
        }
    }

    /**
     * @return string
     */
    public function serviceWorkerUrl() {
        if ($this->driver == 'firebase') {
            return $this->startUrl . 'firebase-messaging-sw.js';
        }

        return $this->startUrl . 'cresenity-messaging-sw.js';
    }

    /**
     * @return string
     */
    public function serviceWorkerScope() {
        return $this->startUrl;
    }

    /**
     * @return string
     */
    public function getSendTokenUrl() {
        return $this->startUrl . $this->sendTokenPath;
    }

    /**
     * @return string
     */
    public function getTokenLocalStorageKey() {
        return $this->tokenLocalStorageKey;
    }

    /**
     * @return null|string
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isDebug() {
        return $this->debug;
    }
}
