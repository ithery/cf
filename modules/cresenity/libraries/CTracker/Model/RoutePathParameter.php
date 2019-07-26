<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 10:59:16 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_RoutePath extends CTracker_Model {

    use CModel_Tracker_TrackerRoutePathParameterTrait;

    protected $table = 'log_route_path_parameter';
    protected $fillable = [
        'log_route_path_id',
        'parameter',
        'value',
    ];

}
