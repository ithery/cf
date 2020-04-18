<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 23, 2019, 2:32:55 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use CApp_Administrator_Fixer_Database as DatabaseFixer;

class Controller_Administrator_App_Fixer_Database_Engine extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Fix Database Table Engine');

        $action = $app->addAction()->setLabel('Check Table Engine Problem')->addClass('btn-primary mb-3');
        $action->onClickListener()->addReloadHandler()->setTarget('data-type-result')->setUrl(curl::base() . 'administrator/app/fixer/database/engine/check');

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
                        ->setTemplate('CApp/Administrator/Fixer/Database/TableEngine/Result')
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
        foreach ($columns as $column) {
            $columnSchema = $tableSchema->getColumn($column);

            if (in_array($columnSchema->getType()->getName(), [CDatabase_Type::STRING, CDatabase_Type::TEXT])) {
                $targetOptions = array(
                    'unsigned' => true,
                );
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

}
