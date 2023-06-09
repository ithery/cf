<?php


class CTracker_Model_SystemClass extends CTracker_Model {
    use CModel_Tracker_TrackerSystemClassTrait;

    protected $table = 'log_system_class';

    protected $fillable = [
        'name',
    ];
}
