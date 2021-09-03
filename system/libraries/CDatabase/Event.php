<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 12:19:51 PM
 */
class CDatabase_Event {
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $dbName;

    /**
     * The database connection instance.
     *
     * @var CDatabase
     */
    public $db;

    /* database */

    /**
     * Create a new event instance.
     *
     * @param CDatabase $db
     *
     * @return void
     */
    public function __construct($db) {
        $this->db = $db;
        $this->dbName = $db->getName();
    }

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

    public static function createOnQueryExecutedEvent($sql, $bindings, $time, $rowsCount, $db) {
        return new CDatabase_Event_OnQueryExecuted($sql, $bindings, $time, $rowsCount, $db);
    }
}
