<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:39:09 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Container for all DBAL events.
 *
 * This class cannot be instantiated.
 *
 */
final class CDatabase_Events {

    /**
     * Private constructor. This class cannot be instantiated.
     */
    private function __construct() {
        
    }

    const postConnect = 'postConnect';
    const onSchemaCreateTable = 'onSchemaCreateTable';
    const onSchemaCreateTableColumn = 'onSchemaCreateTableColumn';
    const onSchemaDropTable = 'onSchemaDropTable';
    const onSchemaAlterTable = 'onSchemaAlterTable';
    const onSchemaAlterTableAddColumn = 'onSchemaAlterTableAddColumn';
    const onSchemaAlterTableRemoveColumn = 'onSchemaAlterTableRemoveColumn';
    const onSchemaAlterTableChangeColumn = 'onSchemaAlterTableChangeColumn';
    const onSchemaAlterTableRenameColumn = 'onSchemaAlterTableRenameColumn';
    const onSchemaColumnDefinition = 'onSchemaColumnDefinition';
    const onSchemaIndexDefinition = 'onSchemaIndexDefinition';

}
