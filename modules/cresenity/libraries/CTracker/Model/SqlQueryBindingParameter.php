<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 10:53:26 PM
 */
class CTracker_Model_SqlQueryBindingParameter extends CTracker_Model {
    use CModel_Tracker_TrackerSqlQueryBindingParameterTrait;

    protected $table = 'log_sql_query_binding_parameter';

    protected $fillable = [
        'log_sql_query_binding_id',
        'name',
        'value',
    ];
}
