<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 9:59:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_QueryArgument extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('queryArgumentModel', 'CTracker_Model_QueryArgument');
        $this->createModel();

        parent::__construct();
    }

}
