<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 10:52:26 PM
 */
class CTracker_Model_SqlQueryBinding extends CTracker_Model {
    use CModel_Tracker_TrackerSqlQueryBindingTrait;

    protected $table = 'log_sql_query_binding';

    protected $fillable = [
        'sha1',
        'serialized',
    ];
}
