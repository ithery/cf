<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:17:38 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_Route extends CTracker_AbstractRepository {

    protected $config;

    public function __construct() {
        $this->className = CTracker::config()->get('routeModel', 'CTracker_Model_Route');
        $this->createModel();
        $this->config = CTracker::config();
        parent::__construct();
    }

    public function isTrackable($route) {
        $forbidden = $this->config->get('do_not_track_routes');
        return
                !$forbidden ||
                !$route->currentRouteName() ||
                !in_array_wildcard($route->currentRouteName(), $forbidden);
    }

    public function pathIsTrackable($path) {
        $forbidden = $this->config->get('do_not_track_paths');
        return
                !$forbidden ||
                empty($path) ||
                !in_array_wildcard($path, $forbidden);
    }

}
