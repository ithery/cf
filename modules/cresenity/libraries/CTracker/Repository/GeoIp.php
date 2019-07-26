<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 2:41:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Repository_GeoIp extends CTracker_AbstractRepository {

    public function __construct() {
        $this->className = CTracker::config()->get('geoIpModel', 'CTracker_Model_GeoIp');
        $this->createModel();

        parent::__construct();
    }

}
