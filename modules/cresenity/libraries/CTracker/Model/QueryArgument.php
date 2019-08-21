<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:00:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_QueryArgument extends CTracker_Model {

    use CModel_Tracker_TrackerQueryArgumentTrait;

    protected $table = 'log_query_argument';
    protected $fillable = [
        'log_query_id',
        'argument',
        'value',
    ];

}
