<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:42:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Device extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('deviceModel', 'CTracker_Model_Device');
        $this->createModel();

        parent::__construct();
    }

}
