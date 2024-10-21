<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:35:03 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_SqlQueryLog extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryLogModel', 'CTracker_Model_SqlQueryLog');
        $this->createModel();

        parent::__construct();
    }

}
