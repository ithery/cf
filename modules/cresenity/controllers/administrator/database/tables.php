<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 12:55:42 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Database_Tables extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();

        $db = CDatabase::instance();
        $app->title('Tables');


        $dbConfig = $db->config();
        $databaseName = carr::get($dbConfig, 'connection.database');
        $table = $app->addTable();
        $table->setTitle('Database: ' . $databaseName);
        $table->setDataFromQuery("SELECT * FROM INFORMATION_SCHEMA.tables as s where TABLE_SCHEMA=" . $db->escape($databaseName) . ";");

        $table->addColumn('TABLE_NAME')->setLabel('Table Name');
        $table->addColumn('ENGINE')->setLabel('Engine');
        $table->addColumn('VERSION')->setLabel('Version');
        $table->setRowActionStyle('btn-dropdown');
        $table->addRowAction()->setLabel('Show Data')->setIcon('fas fa-search')->setLink(curl::base() . 'administrator/tables/{TABLE_NAME}');



        echo $app->render();
    }

}
