<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_Device extends CTracker_Model {
    use CModel_Tracker_TrackerDeviceTrait;

    protected $table = 'log_device';

    protected $fillable = [
        'org_id',
        'kind',
        'model',
        'platform',
        'platform_version',
        'is_mobile',
    ];
}
