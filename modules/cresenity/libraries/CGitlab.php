<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 3:46:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGitlab {

    protected $gitUrl;
    protected $client;
    protected static $instances;

    /**
     * 
     * @param string $gitUrl
     * @param string $token
     * @return CGitlab
     */
    public static function instance($gitUrl = '', $token = null) {

        if (!isset(self::$instances[$gitUrl])) {
            self::$instances[$gitUrl] = new static($gitUrl);
        }
        if ($token != null) {
            self::$instances[$gitUrl]->client()->authenticate($token);
        }
        return self::$instances[$gitUrl];
    }

    /**
     * 
     * @param string $gitUrl
     */
    protected function __construct($gitUrl) {
        $this->gitUrl = $gitUrl;
        $this->client = new CGitlab_Client($gitUrl);
    }

    /**
     * 
     * @return CGitlab_Client
     */
    public function client() {
        return $this->client;
    }

    /**
     * 
     * @return CGitlab_Api_Projects
     */
    public function projects() {
        return $this->client->api('projects');
    }

    /**
     * 
     * @return CGitlab_Api_Users
     */
    public function users() {
        return $this->client->api('users');
    }

    /**
     * 
     * @return CGitlab_Api_Repositories
     */
    public function repositories() {
        return $this->client->api('repositories');
    }

    /**
     * 
     * @return CGitlab_Api_Issues
     */
    public function issues() {
        return $this->client->api('issues');
    }

    /**
     * 
     * @return CGitlab_Api_Groups
     */
    public function groups() {
        return $this->client->api('groups');
    }

    /**
     * 
     * @return CGitlab_Api_Groups
     */
    public function snippets() {
        return $this->client->api('snippets');
    }

}
