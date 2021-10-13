<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 10:38:54 PM
 */
class CTracker_Repository_Connection extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('connectionModel', 'CTracker_Model_Connection');
        $this->createModel();

        parent::__construct();
    }
}
