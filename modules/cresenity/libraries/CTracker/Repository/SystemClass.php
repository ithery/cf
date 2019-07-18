<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:40:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_SystemClass extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('systemClassModel', 'CTracker_Model_SystemClass');
        $this->createModel();

        parent::__construct();
    }

}
