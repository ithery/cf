<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Repository_SqlQueryBindingParameter extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryBindingParameterModel', CTracker_Model_SqlQueryBindingParameter::class);
        $this->createModel();

        parent::__construct();
    }
}
