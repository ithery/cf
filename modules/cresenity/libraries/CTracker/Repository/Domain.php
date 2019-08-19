<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:41:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Domain extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('domainModel', 'CTracker_Model_Domain');
        $this->createModel();

        parent::__construct();
    }

}
