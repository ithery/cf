<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:10:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Agent extends CTracker_Model {

    protected $table = 'log_agent';
    protected $fillable = [
        'name',
        'browser',
        'browser_version',
        'name_hash',
    ];

}
