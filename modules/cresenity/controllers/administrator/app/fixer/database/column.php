<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 12:47:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_App_Fixer_Database_Column extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Fix Database Table Engine');

        $action = $app->addAction()->setLabel('Check Table Column Problem')->addClass('btn-primary mb-3');
        $action->onClickListener()->addReloadHandler()->setTarget('data-type-result')->setUrl(curl::base() . 'administrator/app/fixer/database/column/check');

        $app->addDiv('data-type-result');

        echo $app->render();
    }

    public function check() {
        $app = CApp::instance();

        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        $haveChanged = false;
        foreach ($tables as $table) {
            $sql = $this->getSqlResult($table);

            if (strlen($sql) > 0) {
                $template = $app->addTemplate()
                        ->setTemplate('CApp/Administrator/Fixer/Database/Column/Result')
                        ->setVar('table', $table)
                        ->setVar('sql', $sql);

                $resultBody = $template->section('resultBody');
                $prismCode = $resultBody->addPrismCode();
                $prismCode->setLanguage('sql');
                $prismCode->add($sql);
                $haveChanged = true;
            }
        }
        if (!$haveChanged) {
            $app->addAlert()->setType('success')->add('No Problem Found');
        }

        echo $app->render();
    }

    public function execute($table) {
        $sql = $this->getSqlResult($table);
        $db = CDatabase::instance();
        $errCode = 0;
        $errMessage = '';
        try {
            $db->query($sql);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        echo CApp_Base::jsonResponse($errCode, $errMessage);
    }

    private function getSqlResult($table) {
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
            $options = array();
            $options['default'] = 'NULL';
            $newColumn = new CDatabase_Schema_Column('created', CDatabase_Type::getType(CDatabase_Type::DATETIME), $options);
            $newColumn->setDefault(NULL);
            $newColumn->setNotnull(false);
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('created');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(NULL);
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
            $options = array();
            $newColumn = new CDatabase_Schema_Column('createdby', CDatabase_Type::getType(CDatabase_Type::STRING), $options);
            $newColumn->setDefault(NULL);
            $newColumn->setNotnull(false);
            $newColumn->setLength(255);
            $newColumn->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('createdby');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(NULL);
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
            $options = array();
            $options['default'] = 'NULL';
            $newColumn = new CDatabase_Schema_Column('updated', CDatabase_Type::getType(CDatabase_Type::DATETIME), $options);
            $newColumn->setDefault(NULL);
            $newColumn->setNotnull(false);
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('updated');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(NULL);
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
            $options = array();
            $newColumn = new CDatabase_Schema_Column('updatedby', CDatabase_Type::getType(CDatabase_Type::STRING), $options);
            $newColumn->setDefault(NULL);
            $newColumn->setNotnull(false);
            $newColumn->setLength(255);
            $newColumn->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('updatedby');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(NULL);
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
            $options = array();
            $options['default'] = 'NULL';
            $newColumn = new CDatabase_Schema_Column('deleted', CDatabase_Type::getType(CDatabase_Type::DATETIME), $options);
            $newColumn->setDefault(NULL);
            $newColumn->setNotnull(false);
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('deleted');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(NULL);
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
            $options = array();
            $newColumn = new CDatabase_Schema_Column('deletedby', CDatabase_Type::getType(CDatabase_Type::STRING), $options);
            $newColumn->setDefault(NULL);
            $newColumn->setNotnull(false);
            $newColumn->setLength(255);
            $newColumn->setPlatformOption('collation', 'utf8mb4_unicode_ci');
            $tableDifferences->addedColumns[] = $newColumn;
            $changes++;
        } else {
            $columnSchema = $tableSchema->getColumn('deletedby');
            $targetColumnSchema = clone $columnSchema;
            $targetColumnSchema->setDefault(NULL);
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
            $options = array();
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

}
