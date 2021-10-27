<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 4:09:19 PM
 */
class CTracker_Repository_Language extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('languageModel', CTracker_Model_Language::class);
        $this->createModel();

        parent::__construct();
    }
}
