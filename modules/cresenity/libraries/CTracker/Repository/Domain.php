<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 3:41:59 PM
 */
class CTracker_Repository_Domain extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('domainModel', CTracker_Model_Domain::class);
        $this->createModel();

        parent::__construct();
    }
}
