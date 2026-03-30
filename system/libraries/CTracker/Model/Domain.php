<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_Domain extends CTracker_Model {
    use CModel_Tracker_TrackerDomainTrait;

    protected $table = 'log_domain';

    protected $fillable = [
        'name',
    ];
}
