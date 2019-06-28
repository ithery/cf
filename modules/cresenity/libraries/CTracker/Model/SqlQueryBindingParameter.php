<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 10:53:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Model_SqlQueryBindingParameter extends CTracker_Model {

    protected $table = 'log_sql_query_binding_parameter';
    protected $fillable = [
        'log_sql_query_binding_id',
        'name',
        'value',
    ];

}
