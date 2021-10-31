<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 10:35:43 PM
 */
class CTracker_Repository_SqlQueryBinding extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryBindingModel', 'CTracker_Model_SqlQueryBinding');
        $this->createModel();

        parent::__construct();
    }
}
