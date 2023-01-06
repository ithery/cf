<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    'undefined_group' => 'The :group group is not defined in your configuration.',
    'sql_error' => 'There was an SQL error: :error',
    'connection_error' => 'There was an error connecting to the database: :error',
    'invalid_dsn' => 'The DSN you supplied is not valid: :error',
    'must_use_set' => 'You must set a SET clause for your query.',
    'must_use_where' => 'You must set a WHERE clause for your query.',
    'must_use_table' => 'You must set a database table for your query.',
    'table_not_found' => 'Table :table does not exist in your database.',
    'not_implemented' => 'The method you called, :method, is not supported by this driver.',
    'result_read_only' => 'Query results are read only.'
];
