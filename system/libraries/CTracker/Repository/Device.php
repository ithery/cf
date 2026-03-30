<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Repository_Device extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('deviceModel', CTracker_Model_Device::class);
        $this->createModel();

        parent::__construct();
    }
}
