<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 9:51:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Query extends CTracker_Model {

    use CModel_Tracker_TrackerQueryTrait;

    protected $table = 'log_query';
    protected $fillable = [
        'query',
    ];

}
