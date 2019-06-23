<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 9:49:19 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Query extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('queryModel', 'CTracker_Model_Query');
        $this->createModel();

        parent::__construct();
    }

}
