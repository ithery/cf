<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_RoutePath extends CTracker_Model {
    use CModel_Tracker_TrackerRoutePathTrait;

    protected $table = 'log_route_path';

    protected $fillable = [
        'log_route_id',
        'path',
    ];
}
