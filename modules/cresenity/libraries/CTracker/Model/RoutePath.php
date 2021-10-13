<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 20, 2019, 10:54:21 PM
 */
class CTracker_Model_RoutePath extends CTracker_Model {
    use CModel_Tracker_TrackerRoutePathTrait;

    protected $table = 'log_route_path';

    protected $fillable = [
        'log_route_id',
        'path',
    ];
}
