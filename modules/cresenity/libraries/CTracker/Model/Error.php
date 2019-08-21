<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 20, 2019, 11:10:36 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_Error extends CTracker_Model {

    use CModel_Tracker_TrackerErrorTrait;

    protected $table = 'log_error';
    protected $fillable = [
        'message',
        'code',
    ];

}
