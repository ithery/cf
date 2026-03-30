<?php

defined('SYSPATH') or die('No direct access allowed.');

use Ramsey\Uuid\Uuid as UUID;

class CTracker_Repository_Cookie extends CTracker_AbstractRepository {
    protected $config;

    public function __construct() {
        $this->className = CTracker::config()->get('cookieModel', CTracker_Model_Cookie::class);
        $this->createModel();
        $this->config = CTracker::config();
        parent::__construct();
    }

    public function getId() {
        if (!$this->config->isLogCookie()) {
            return;
        }
        $cookieUuid = CTracker::populator()->get('cookie.uuid');

        return $this->findOrCreate(['uuid' => $cookieUuid]);
    }
}
