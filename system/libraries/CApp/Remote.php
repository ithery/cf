<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 4:33:53 AM
 */
class CApp_Remote {
    /**
     * @var array array of CApp_Remote
     */
    protected static $instance = [];

    /**
     * @var int protocol of this instance
     */
    protected $port;

    /**
     * @var string protocol of this instance
     */
    protected $protocol;

    /**
     * @var string domain of this instance
     */
    protected $domain;

    /**
     * @var CApp_Remote_Client
     */
    protected $client;

    /**
     * Variable to store available options for this object
     *
     * @var options
     */
    protected $options;

    /**
     * @param string $domain
     * @param mixed  $options
     *
     * @return CApp_Remote
     */
    public static function instance($domain = null, $options = []) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if (!is_array(self::$instance)) {
            self::$instance = [];
        }
        if (!isset(self::$instance[$domain])) {
            self::$instance[$domain] = new static($domain, $options);
        }
        return self::$instance[$domain];
    }

    /**
     * @param string $domain
     * @param mixed  $options
     */
    protected function __construct($domain, $options = []) {
        $this->domain = $domain;
        $this->options = $options;
        $this->protocol = carr::get($options, 'protocol', 'http');
        $this->port = carr::get($options, 'port', 80);
        $this->client = new CApp_Remote_Client($this);
    }

    /**
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return CApp_Remote_Client
     */
    public function client() {
        return $this->client;
    }
}
