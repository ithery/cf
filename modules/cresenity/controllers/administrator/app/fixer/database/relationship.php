<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 13, 2019, 12:54:01 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_App_Fixer_Database_Relationship extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Fix Database Relationship');

        $action = $app->addAction()->setLabel('Check Relationship Problem')->addClass('btn-primary mb-3');
        $action->onClickListener()->addReloadHandler()->setTarget('relationship-result')->setUrl(curl::base() . 'administrator/app/fixer/database/relationship/check');

        $app->addDiv('relationship-result');

        echo $app->render();
    }

    public function check() {
        $app = CApp::instance();

        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        $schema = $schemaManager->createSchema();

        foreach ($tables as $table) {
            $sql = $this->getSqlResult($table);

            if (strlen($sql) > 0) {
                $template = $app->addTemplate()
                        ->setTemplate('CApp/Administrator/Fixer/Database/Relationship/Result')
                        ->setVar('table', $table)
                        ->setVar('sql', $sql);

                $resultBody = $template->section('resultBody');
                $prismCode = $resultBody->addPrismCode();
                $prismCode->setLanguage('sql');
                $prismCode->setHaveCopyToClipboard();
                $prismCode->setHaveSelectCode();
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

                if (!in_array($columnSchema->getType()->getName(), array(CDatabase_Type::INTEGER, CDatabase_Type::SMALLINT, CDatabase_Type::BIGINT))) {
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

                    if ($foreignKey->getLocalTable()->getName() == $table && $foreignKey->getLocalColumns() == array($column)) {
                        //already have foreign key
                        $targetForeignKey = new CDatabase_Schema_ForeignKeyConstraint(array($column), $tableRelation, array($column), $foreignKey->getName(), array("onUpdate" => "RESTRICT", "onDelete" => "RESTRICT"));
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

                $fkConstraint = new CDatabase_Schema_ForeignKeyConstraint(array($column), $tableRelation, array($column), null, array("onUpdate" => "RESTRICT", "onDelete" => "RESTRICT"));

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
