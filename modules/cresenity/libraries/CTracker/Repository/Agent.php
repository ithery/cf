<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:09:58 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Agent extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = 'CTracker_Model_Agent';
        $this->createModel();

        parent::__construct();
    }

}
