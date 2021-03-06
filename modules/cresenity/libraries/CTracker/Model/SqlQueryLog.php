<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 10:51:51 PM
 */
class CTracker_Model_SqlQueryLog extends CTracker_Model {
    use CModel_Tracker_TrackerSqlQueryLogTrait;

    protected $table = 'log_sql_query_log';

    protected $fillable = [
        'log_log_id',
        'log_sql_query_id',
    ];
}
