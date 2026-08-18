<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTracker_Model_SqlQueryBindingParameter extends CTracker_Model {
    use CModel_Tracker_TrackerSqlQueryBindingParameterTrait;

    protected $table = 'log_sql_query_binding_parameter';

    protected $fillable = [
        'log_sql_query_binding_id',
        'name',
        'value',
    ];
}
