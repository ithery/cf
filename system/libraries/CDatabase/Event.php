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
    const onSchemaCreateTable = 'CDatabase_Event_Schema_OnSchemaCreateTable';
    const onSchemaCreateTableColumn = 'CDatabase_Event_Schema_OnSchemaCreateTableColumn';
    const onSchemaDropTable = 'CDatabase_Event_Schema_OnSchemaDropTable';
    const onSchemaAlterTable = 'CDatabase_Event_Schema_OnSchemaAlterTable';
    const onSchemaAlterTableAddColumn = 'CDatabase_Event_Schema_OnSchemaAlterTableAddColumn';
    const onSchemaAlterTableRemoveColumn = 'CDatabase_Event_Schema_OnSchemaAlterTableRemoveColumn';
    const onSchemaAlterTableChangeColumn = 'CDatabase_Event_Schema_OnSchemaAlterTableChangeColumn';
    const onSchemaAlterTableRenameColumn = 'CDatabase_Event_Schema_OnSchemaAlterTableRenameColumn';
    const onSchemaColumnDefinition = 'CDatabase_Event_Schema_OnSchemaColumnDefinition';
    const onSchemaIndexDefinition = 'CDatabase_Event_Schema_OnSchemaIndexDefinition';

    public static function createOnQueryExecutedListener($sql, $bindings, $time, $rowsCount, $db) {
        return new CDatabase_Event_OnQueryExecutedListener($sql, $bindings, $time, $rowsCount, $db);
    }

}
