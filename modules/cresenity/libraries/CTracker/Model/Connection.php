<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:51:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Connection extends CTracker_Model {

    use CModel_Tracker_TrackerConnectionTrait;

    protected $table = 'log_connection';
    protected $fillable = [
        'name',
    ];

}
