<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_RoutePathParameter extends CTracker_Model {
    use CModel_Tracker_TrackerRoutePathParameterTrait;

    protected $table = 'log_route_path_parameter';

    protected $fillable = [
        'log_route_path_id',
        'parameter',
        'value',
    ];
}
