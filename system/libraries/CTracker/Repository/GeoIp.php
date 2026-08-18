<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Repository_GeoIp extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('geoIpModel', CTracker_Model_GeoIp::class);
        $this->createModel();

        parent::__construct();
    }
}
