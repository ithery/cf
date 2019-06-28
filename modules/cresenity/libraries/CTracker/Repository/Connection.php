<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:38:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Connection extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('connectionModel', 'CTracker_Model_Connection');
        $this->createModel();

        parent::__construct();
    }

}
