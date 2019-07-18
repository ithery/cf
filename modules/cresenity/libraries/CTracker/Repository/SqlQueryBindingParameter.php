<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:36:15 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_SqlQueryBindingParameter extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryBindingParameterModel', 'CTracker_Model_SqlQueryBindingParameter');
        $this->createModel();

        parent::__construct();
    }

}