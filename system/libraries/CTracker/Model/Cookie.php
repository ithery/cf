<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 3:39:23 PM
 */
class CTracker_Model_Cookie extends CTracker_Model {
    use CModel_Tracker_TrackerCookieTrait;

    protected $table = 'log_cookie';

    protected $fillable = [
        'uuid',
    ];
}
