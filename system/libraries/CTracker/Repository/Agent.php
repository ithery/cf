<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Repository_Agent extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('agentModel', CTracker_Model_Agent::class);
        $this->createModel();

        parent::__construct();
    }
}
