<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:29:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Ramsey\Uuid\Uuid as UUID;

class CTracker_Repository_Cookie extends CTracker_AbstractRepository {

    /**
     *
     * @var CHTTP_Request
     */
    private $request;

    /**
     *
     * @var CCookie_Jar
     */
    private $cookieJar;

    /**
     *
     * @var CTracker_Config
     */
    private $config;

    public function __construct() {
        $this->className = CTracker::config()->get('cookieModel', 'CTracker_Model_Cookie');
        $this->createModel();
        $this->config = CTracker::config();
        $this->request = CHTTP::request();
        $this->cookieJar = CCookie::jar();
        parent::__construct();
    }

    public function getId() {
        if (!$this->config->isLogCookie()) {
            return;
        }
        if (!$cookie = $this->request->cookie($this->config->cookieNamespace())) {

            $cookie = (string) UUID::uuid4();

            $this->cookieJar->queue($this->config->cookieNamespace(), $cookie, 0);

            /**
             * directly send cookie, TODO send queued cookies when try to render response
             */
            foreach ($this->cookieJar->getQueuedCookies() as $cookieItem) {
                $cookiesString = 'Set-Cookie: ' . $cookieItem . "\r\n";
                header($cookiesString);
            }
        }
        return $this->findOrCreate(['uuid' => $cookie]);
    }

}
