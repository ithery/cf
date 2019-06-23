<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:36:52 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CTracker_Model_SqlQuery extends CTracker_Model {

    protected $table = 'log_sql_query';
    protected $fillable = [
        'sha1',
        'statement',
        'time',
        'log_connection_id',
    ];

}
