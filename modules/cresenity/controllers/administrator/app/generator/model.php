<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 12, 2019, 3:33:43 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class Controller_Administrator_App_Generator_Model extends CApp_Administrator_Controller_User {
    
    public function tables() {
        $app = CApp::instance();
        
        $db = CDatabase::instance();
        $schemaManager = $db->getSchemaManager();
        $tableNames = $schemaManager->listTableNames();
        
        $tabList = $app->addTabList()->setAjax(true);
        foreach($tableNames as $tableName) {
            $tabList->addTab()->setLabel($tableName)->setAjaxUrl(curl::base().'administrator/app/generator/model/table/'.$tableName);
        }
        
        
        echo $app->render();
    }
    
    public function table($tableName) {
        $app = CApp::instance();
        
        $app->addH5()->add($tableName);
        
        $modelGenerator = CApp_Project::modelGenerator();
        $options=array();
        $options['table']=$tableName;
        $options['prefix']=$app->config('prefix');
        
        $text = $modelGenerator->generate($options);
        
        $prismCode = $app->addPrismCode();
        $prismCode->setHaveSelectCode();
        $prismCode->setHaveCopyToClipboard();
        $prismCode->add(chtml::specialchars($text));
        //$app->addControl('generated','textarea')->setValue($text);
        echo $app->render();
    }
}