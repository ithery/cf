<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:09:19 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Language extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('languageModel', 'CTracker_Model_Language');
        $this->createModel();

        parent::__construct();
    }

}
