<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_SqlQuery extends CTracker_Model {
    use CModel_Tracker_TrackerSqlQueryTrait;

    protected $table = 'log_sql_query';

    protected $fillable = [
        'sha1',
        'statement',
        'time',
        'log_connection_id',
    ];
}
