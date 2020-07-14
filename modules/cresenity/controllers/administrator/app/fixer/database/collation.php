<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 17, 2019, 2:15:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Administrator_Fixer_Database as DatabaseFixer;

class Controller_Administrator_App_Fixer_Database_Collation extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Fix Database Collation');

        $action = $app->addAction()->setLabel('Check Collation Problem')->addClass('btn-primary mb-3');
        $action->onClickListener()->addReloadHandler()->setTarget('data-type-result')->setUrl(curl::base() . 'administrator/app/fixer/database/collation/check');

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
            $sql = DatabaseFixer::sqlCollation($table);

            if (strlen($sql) > 0) {
                $template = $app->addTemplate()
                        ->setTemplate('CApp/Administrator/Fixer/Database/Collation/Result')
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
        $sql = DatabaseFixer::sqlCollation($table);
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
