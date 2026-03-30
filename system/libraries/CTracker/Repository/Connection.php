<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Repository_Connection extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('connectionModel', CTracker_Model_Connection::class);
        $this->createModel();

        parent::__construct();
    }
}
