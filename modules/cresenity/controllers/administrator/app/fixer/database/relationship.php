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
            $columnsData = $schemaManager->listTableColumns($table);
            $columns = array_keys($columnsData);
            $tableSchema = $schema->getTable($table);
//            $indexes = $tableSchema->getIndexes();
            $foreignKeys = $tableSchema->getForeignKeys();
            $app->addH2()->add($table);
            $app->add($foreignKeys);
            foreach ($columns as $column) {
                if (cstr::endsWith($column, '_id')) {
                    $tableRelation = substr($column, 0, -3);
                    if ($tableRelation == $table) {
                        //this is primary key
                        continue;
                    }
                    $template = $app->addTemplate()->setTemplate('CApp/Administrator/Fixer/Database/Relationship/Result');
                    $template->setVar('table', $table);
                    $template->setVar('column', $column);
                }
            }
        }

        echo $app->render();
    }

}
