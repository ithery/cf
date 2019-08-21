<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:51:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_SqlQueryLog extends CTracker_Model {

    use CModel_Tracker_TrackerSqlQueryLogTrait;

    protected $table = 'log_sql_query_log';
    protected $fillable = [
        'log_log_id',
        'log_sql_query_id',
    ];

}
