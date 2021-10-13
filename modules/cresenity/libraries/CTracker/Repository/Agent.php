<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 3:09:58 PM
 */
class CTracker_Repository_Agent extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('agentModel', 'CTracker_Model_Agent');
        $this->createModel();

        parent::__construct();
    }
}
