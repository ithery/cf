<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Repository_SqlQueryBinding extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryBindingModel', 'CTracker_Model_SqlQueryBinding');
        $this->createModel();

        parent::__construct();
    }
}
