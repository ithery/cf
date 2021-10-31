<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 10:36:15 PM
 */
class CTracker_Repository_SqlQueryBindingParameter extends CTracker_AbstractRepository {
    public function __construct() {
        $this->className = CTracker::config()->get('sqlQueryBindingParameterModel', CTracker_Model_SqlQueryBindingParameter::class);
        $this->createModel();

        parent::__construct();
    }
}
