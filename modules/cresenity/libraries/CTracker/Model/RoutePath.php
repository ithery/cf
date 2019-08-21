<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 10:54:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_RoutePath extends CTracker_Model {

    use CModel_Tracker_TrackerRoutePathTrait;

    protected $table = 'log_route_path';
    protected $fillable = [
        'log_route_id',
        'path',
    ];

}
