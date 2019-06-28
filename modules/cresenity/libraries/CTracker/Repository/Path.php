<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:33:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Path extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = 'CTracker_Model_Path';
        $this->createModel();

        parent::__construct();
    }

}
