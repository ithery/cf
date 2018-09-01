<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 12:19:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Event extends CEvent {
    /* database */

    const onQueryExecuted = 'CDatabase_Event_OnQueryExecuted';
    const onPostConnect = 'CDatabase_Event_OnPostConnect';
    /* schema */
    const onSchemaCreateTable = 'CDatabase_Event_Schema_OnCreateTable';
    const onSchemaCreateTableColumn = 'CDatabase_Event_Schema_OnCreateTableColumn';
    const onSchemaDropTable = 'CDatabase_Event_Schema_OnDropTable';
    const onSchemaAlterTable = 'CDatabase_Event_Schema_OnAlterTable';
    const onSchemaAlterTableAddColumn = 'CDatabase_Event_Schema_OnAlterTableAddColumn';
    const onSchemaAlterTableRemoveColumn = 'CDatabase_Event_Schema_OnAlterTableRemoveColumn';
    const onSchemaAlterTableChangeColumn = 'CDatabase_Event_Schema_OnAlterTableChangeColumn';
    const onSchemaAlterTableRenameColumn = 'CDatabase_Event_Schema_OnAlterTableRenameColumn';
    const onSchemaColumnDefinition = 'CDatabase_Event_Schema_OnColumnDefinition';
    const onSchemaIndexDefinition = 'CDatabase_Event_Schema_OnIndexDefinition';

    public static function createOnQueryExecutedListener($sql, $bindings, $time, $rowsCount, $db) {
        return new CDatabase_Event_OnQueryExecutedListener($sql, $bindings, $time, $rowsCount, $db);
    }

}
