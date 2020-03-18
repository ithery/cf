<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 13, 2019, 12:54:01 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use CApp_Administrator_Fixer_Database as DatabaseFixer;

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
        $haveChanged = false;
        foreach ($tables as $table) {
            $sql = DatabaseFixer::sqlRelationship($table);

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
                $haveChanged = true;
            }
        }

        if (!$haveChanged) {
            $app->addAlert()->setType('success')->add('No Problem Found');
        }

        echo $app->render();
    }

    public function execute($table) {
        $sql = DatabaseFixer::sqlRelationship($table);
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

}
