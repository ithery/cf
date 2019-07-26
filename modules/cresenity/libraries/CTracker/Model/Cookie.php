<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:39:23 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Cookie extends CTracker_Model {

    use CModel_Tracker_TrackerCookieTrait;

    protected $table = 'log_cookie';
    protected $fillable = [
        'uuid',
    ];

}
