<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Administrator_App_Fixer_Table extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Database Fixer');
        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        if (carr::isArray($tables) && carr::count($tables)>0) {
            $tabList = $app->addTabList()->setAjax(true)->setTabPosition('left');
            foreach ($tables as $table) {
                $tab = $tabList->addTab()->setLabel($table)->setAjaxUrl(curl::base() . 'administrator/app/fixer/table/tab/'.$table);
        
            }
        }


        echo $app->render();
    }
    
    public function tab($table) {
        $app = CApp::instance();
        $tabList = $app->addTabList()->setAjax(true)->setTabPosition('left');
        $tab = $tabList->addTab()->setLabel('Column')->setAjaxUrl(curl::base() . 'administrator/app/fixer/table/column/index/'.$table);
        $tab = $tabList->addTab()->setLabel('Data Type')->setAjaxUrl(curl::base() . 'administrator/app/fixer/table/datatype/index/'.$table);
        $tab = $tabList->addTab()->setLabel('Relationship')->setAjaxUrl(curl::base() . 'administrator/app/fixer/table/relationship/index/'.$table);
        $tab = $tabList->addTab()->setLabel('Collation')->setAjaxUrl(curl::base() . 'administrator/app/fixer/table/collation/index/'.$table);

        echo $app->render();
    }

}
