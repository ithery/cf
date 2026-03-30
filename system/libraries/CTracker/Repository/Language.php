<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Repository_Language extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('languageModel', CTracker_Model_Language::class);
        $this->createModel();

        parent::__construct();
    }
}
