<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_Cookie extends CTracker_Model {
    use CModel_Tracker_TrackerCookieTrait;

    protected $table = 'log_cookie';

    protected $fillable = [
        'uuid',
    ];
}
