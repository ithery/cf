<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:35:43 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CTracker_Repository_SqlQueryBinding extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryBindingModel', 'CTracker_Model_SqlQueryBinding');
        $this->createModel();

        parent::__construct();
    }

}