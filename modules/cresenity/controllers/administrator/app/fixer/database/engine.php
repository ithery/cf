<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 23, 2019, 2:32:55 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */


/**
 * @author Hery Kurniawan
 * @since Aug 13, 2019, 1:21:26 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_App_Fixer_Database_Engine extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Fix Database Table Engine');

        $action = $app->addAction()->setLabel('Check Table Engine Problem')->addClass('btn-primary mb-3');
        $action->onClickListener()->addReloadHandler()->setTarget('data-type-result')->setUrl(curl::base() . 'administrator/app/fixer/database/datatype/check');

        $app->addDiv('data-type-result');

        echo $app->render();
    }

    public function check() {
        $app = CApp::instance();

        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $tables = $schemaManager->listTableNames();

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
            }
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


            if (cstr::endsWith($column, '_id')) {
                $targetOptions = array(
                    'unsigned' => true,
                );

                if(!in_array($columnSchema->getType()->getName(), array(CDatabase_Type::INTEGER, CDatabase_Type::SMALLINT, CDatabase_Type::BIGINT))) {
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

}
