<?php

class CApp_Administrator_Fixer_Database {
    public static function sqlCollation($table) {
        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $columnsData = $schemaManager->listTableColumns($table);
        $columns = array_keys($columnsData);
        $tableSchema = $schema->getTable($table);
        $changes = 0;
        $tableDifferences = new CDatabase_Schema_Table_Diff($table);

        $dbPlatform = $db->getDatabasePlatform();
        $comparator = new CDatabase_Schema_Comparator();
        foreach ($columns as $column) {
            $columnSchema = $tableSchema->getColumn($column);

            if (in_array($columnSchema->getType()->getName(), [CDatabase_Type::STRING, CDatabase_Type::TEXT])) {
                $targetOptions = [
                    'unsigned' => true,
                ];
                $targetColumnSchema = clone $columnSchema;
                $targetColumnSchema->setPlatformOption('collation', 'utf8mb4_unicode_ci');
                // See if column has changed properties in table 2.
                $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

                if (!empty($changedProperties)) {
                    $columnDiff = new CDatabase_Schema_Column_Diff($column, $targetColumnSchema, $changedProperties);
                    $columnDiff->fromColumn = $columnSchema;
                    $tableDifferences->changedColumns[$column] = $columnDiff;
                    $changes++;
                }
            }
        }
        $sql = null;
        if ($changes) {
            $sqlArray = $dbPlatform->getAlterTableSQL($tableDifferences);
            $sql = implode(";\n", $sqlArray);
            $sql .= ';';
        }
        return $sql;
    }

    public static function sqlColumn($table) {
        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $columnsData = $schemaManager->listTableColumns($table);
        $columns = array_keys($columnsData);
        $tableSchema = $schema->getTable($table);
        $changes = 0;
        $tableDifferences = new CDatabase_Schema_Table_Diff($table);

        $dbPlatform = $db->getDatabasePlatform();
        $comparator = new CDatabase_Schema_Comparator();
        $addedColumns = [];

        if (!in_array('created', $columns)) {
            $options = [];
            $options['default'] = 'NULL';
            $newColumn = new CDatabase_Schema_Column('created', CDatabase_Type::getType(CDatabase_Type::DATETIME), $options);
            $newColumn->setDefault(null);
            $newColumn->setNotnull(false);
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('created');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(null);
            $targetColumnSchema->setNotnull(false);
            $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

            if (!empty($changedProperties)) {
                $columnDiff = new CDatabase_Schema_Column_Diff('created', $targetColumnSchema, $changedProperties);
                $columnDiff->fromColumn = $columnSchema;
                $tableDifferences->changedColumns['created'] = $columnDiff;
                $changes++;
            }
        }

        if (!in_array('createdby', $columns)) {
            $options = [];
            $newColumn = new CDatabase_Schema_Column('createdby', CDatabase_Type::getType(CDatabase_Type::STRING), $options);
            $newColumn->setDefault(null);
            $newColumn->setNotnull(false);
            $newColumn->setLength(255);
            $newColumn->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('createdby');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(null);
            $targetColumnSchema->setNotnull(false);
            $targetColumnSchema->setLength(255);
            $targetColumnSchema->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

            if (!empty($changedProperties)) {
                $columnDiff = new CDatabase_Schema_Column_Diff('createdby', $targetColumnSchema, $changedProperties);
                $columnDiff->fromColumn = $columnSchema;
                $tableDifferences->changedColumns['createdby'] = $columnDiff;
                $changes++;
            }
        }

        if (!in_array('updated', $columns)) {
            $options = [];
            $options['default'] = 'NULL';
            $newColumn = new CDatabase_Schema_Column('updated', CDatabase_Type::getType(CDatabase_Type::DATETIME), $options);
            $newColumn->setDefault(null);
            $newColumn->setNotnull(false);
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('updated');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(null);
            $targetColumnSchema->setNotnull(false);
            $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

            if (!empty($changedProperties)) {
                $columnDiff = new CDatabase_Schema_Column_Diff('updated', $targetColumnSchema, $changedProperties);
                $columnDiff->fromColumn = $columnSchema;
                $tableDifferences->changedColumns['updated'] = $columnDiff;
                $changes++;
            }
        }

        if (!in_array('updatedby', $columns)) {
            $options = [];
            $newColumn = new CDatabase_Schema_Column('updatedby', CDatabase_Type::getType(CDatabase_Type::STRING), $options);
            $newColumn->setDefault(null);
            $newColumn->setNotnull(false);
            $newColumn->setLength(255);
            $newColumn->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('updatedby');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(null);
            $targetColumnSchema->setNotnull(false);
            $targetColumnSchema->setLength(255);
            $targetColumnSchema->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

            if (!empty($changedProperties)) {
                $columnDiff = new CDatabase_Schema_Column_Diff('updatedby', $targetColumnSchema, $changedProperties);
                $columnDiff->fromColumn = $columnSchema;
                $tableDifferences->changedColumns['updatedby'] = $columnDiff;
                $changes++;
            }
        }

        if (!in_array('deleted', $columns)) {
            $options = [];
            $options['default'] = 'NULL';
            $newColumn = new CDatabase_Schema_Column('deleted', CDatabase_Type::getType(CDatabase_Type::DATETIME), $options);
            $newColumn->setDefault(null);
            $newColumn->setNotnull(false);
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('deleted');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(null);
            $targetColumnSchema->setNotnull(false);
            $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

            if (!empty($changedProperties)) {
                $columnDiff = new CDatabase_Schema_Column_Diff('deleted', $targetColumnSchema, $changedProperties);
                $columnDiff->fromColumn = $columnSchema;
                $tableDifferences->changedColumns['deleted'] = $columnDiff;
                $changes++;
            }
        }

        if (!in_array('deletedby', $columns)) {
            $options = [];
            $newColumn = new CDatabase_Schema_Column('deletedby', CDatabase_Type::getType(CDatabase_Type::STRING), $options);
            $newColumn->setDefault(null);
            $newColumn->setNotnull(false);
            $newColumn->setLength(255);
            $newColumn->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('deletedby');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(null);
            $targetColumnSchema->setNotnull(false);
            $targetColumnSchema->setLength(255);
            $targetColumnSchema->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

            if (!empty($changedProperties)) {
                $columnDiff = new CDatabase_Schema_Column_Diff('deletedby', $targetColumnSchema, $changedProperties);
                $columnDiff->fromColumn = $columnSchema;
                $tableDifferences->changedColumns['deletedby'] = $columnDiff;
                $changes++;
            }
        }

        if (!in_array('status', $columns)) {
            $options = [];
            $newColumn = new CDatabase_Schema_Column('status', CDatabase_Type::getType(CDatabase_Type::INTEGER), $options);
            $newColumn->setDefault(1);
            $newColumn->setNotnull(true);
            $newColumn->setLength(1);
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('status');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(1);
            $targetColumnSchema->setNotnull(true);
            $targetColumnSchema->setLength(1);
            $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

            if (!empty($changedProperties)) {
                $columnDiff = new CDatabase_Schema_Column_Diff('status', $targetColumnSchema, $changedProperties);
                $columnDiff->fromColumn = $columnSchema;
                $tableDifferences->changedColumns['status'] = $columnDiff;
                $changes++;
            }
        }
        $sql = null;
        if ($changes) {
            $sqlArray = $dbPlatform->getAlterTableSQL($tableDifferences);
            $sql = implode(";\n", $sqlArray);
            $sql .= ';';
        }
        return $sql;
    }

    public static function sqlDataType($table) {
        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $columnsData = $schemaManager->listTableColumns($table);
        $columns = array_keys($columnsData);
        $tableSchema = $schema->getTable($table);
        $changes = 0;
        $tableDifferences = new CDatabase_Schema_Table_Diff($table);

        $dbPlatform = $db->getDatabasePlatform();
        $comparator = new CDatabase_Schema_Comparator();
        foreach ($columns as $column) {
            $columnSchema = $tableSchema->getColumn($column);

            if (cstr::endsWith($column, '_id')) {
                $targetOptions = [
                    'unsigned' => true,
                ];

                if (!in_array($columnSchema->getType()->getName(), [CDatabase_Type::INTEGER, CDatabase_Type::SMALLINT, CDatabase_Type::BIGINT])) {
                    continue;
                }

                if ($table == 'cloud_messaging' && $column == 'registration_id') {
                    continue;
                }
                if (in_array($table, ['pushnotif_queue', 'pushnotif_queue_member']) && $column == 'reg_id') {
                    continue;
                }

                if (in_array($table, ['log_activity', 'log_session', 'log_login', 'log_login_fail']) && $column == 'session_id') {
                    continue;
                }
                if (in_array($table, ['mobile_app_requirement']) && $column == 'firebase_sender_id') {
                    continue;
                }

                //$targetColumnSchema = new CDatabase_Schema_Column($column, CDatabase_Type::getType(CDatabase_Type::BIGINT), $targetOptions);
                $targetColumnSchema = clone $columnSchema;
                $targetColumnSchema->setType(CDatabase_Type::getType(CDatabase_Type::BIGINT));
                $targetColumnSchema->setUnsigned(true);

                // See if column has changed properties in table 2.
                $changedProperties = $comparator->diffColumn($columnSchema, $targetColumnSchema);

                if (!empty($changedProperties)) {
                    $columnDiff = new CDatabase_Schema_Column_Diff($column, $targetColumnSchema, $changedProperties);
                    $columnDiff->fromColumn = $columnSchema;
                    $tableDifferences->changedColumns[$column] = $columnDiff;
                    $changes++;
                }
            }
        }
        $sql = null;
        if ($changes) {
            $sqlArray = $dbPlatform->getAlterTableSQL($tableDifferences);
            $sql = implode(";\n", $sqlArray);
            $sql .= ';';
        }
        return $sql;
    }

    public static function sqlRelationship($table) {
        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        $schema = $schemaManager->createSchema();
        $columnsData = $schemaManager->listTableColumns($table);
        $columns = array_keys($columnsData);
        $tableSchema = $schema->getTable($table);
        $changes = 0;
        $tableDifferences = new CDatabase_Schema_Table_Diff($table);

        $dbPlatform = $db->getDatabasePlatform();
        $comparator = new CDatabase_Schema_Comparator();
        $app = CApp::instance();
        $foreignKeys = $tableSchema->getForeignKeys();

        foreach ($columns as $column) {
            $columnSchema = $tableSchema->getColumn($column);
            if (cstr::endsWith($column, '_id')) {
                $tableRelation = substr($column, 0, -3);
                if ($tableRelation == $table) {
                    //this is primary key
                    continue;
                }

                if (!in_array($columnSchema->getType()->getName(), [CDatabase_Type::INTEGER, CDatabase_Type::SMALLINT, CDatabase_Type::BIGINT])) {
                    continue;
                }
                if ($table == 'cloud_messaging' && $column == 'registration_id') {
                    continue;
                }
                if (in_array($table, ['pushnotif_queue', 'pushnotif_queue_member']) && $column == 'reg_id') {
                    continue;
                }

                if (in_array($table, ['log_activity', 'log_session', 'log_login', 'log_login_fail']) && $column == 'session_id') {
                    continue;
                }
                if (in_array($table, ['mobile_app_requirement']) && $column == 'firebase_sender_id') {
                    continue;
                }
                if (!in_array($tableRelation, $tables)) {
                    //table relation is not found in current list of tables
                    continue;
                }

                $haveForeign = false;

                foreach ($foreignKeys as $foreignKey) {
                    if ($foreignKey->getLocalTable()->getName() == $table && $foreignKey->getLocalColumns() == [$column]) {
                        //already have foreign key
                        $targetForeignKey = new CDatabase_Schema_ForeignKeyConstraint([$column], $tableRelation, [$column], $foreignKey->getName(), ['onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT']);
                        if ($comparator->diffForeignKey($targetForeignKey, $foreignKey)) {
                            $tableDifferences->changedForeignKeys[] = $targetForeignKey;
                            $changes++;
                        }
                        $haveForeign = true;
                        break;
                    }
                }
                if ($haveForeign) {
                    continue;
                }

                $fkConstraint = new CDatabase_Schema_ForeignKeyConstraint([$column], $tableRelation, [$column], null, ['onUpdate' => 'RESTRICT', 'onDelete' => 'RESTRICT']);

                $tableDifferences->addedForeignKeys[] = $fkConstraint;
                $changes++;
            }
        }
        $sql = null;
        if ($changes) {
            $sqlArray = $dbPlatform->getAlterTableSQL($tableDifferences);
            $sql = implode(";\n", $sqlArray);
            $sql .= ';';
        }
        return $sql;
    }
}
