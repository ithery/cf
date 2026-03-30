<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_SqlQueryBinding extends CTracker_Model {
    use CModel_Tracker_TrackerSqlQueryBindingTrait;

    protected $table = 'log_sql_query_binding';

    protected $fillable = [
        'sha1',
        'serialized',
    ];
}
