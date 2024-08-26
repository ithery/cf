<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 13, 2019, 1:21:26 AM
 */

use CApp_Administrator_Fixer_Database as DatabaseFixer;

class Controller_Administrator_App_Fixer_Database_Datatype extends CApp_Administrator_Controller_User {
    public function index() {
        $app = CApp::instance();
        $app->title('Fix Database Data Type');

        $action = $app->addAction()->setLabel('Check Data Type Problem')->addClass('btn-primary mb-3');
        $action->onClickListener()->addReloadHandler()->setTarget('data-type-result')->setUrl(curl::base() . 'administrator/app/fixer/database/datatype/check');

        $app->addDiv('data-type-result');

        echo $app->render();
    }

    public function check() {
        $app = CApp::instance();

        $db = c::db();
        $schemaManager = $db->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        $haveChanged = false;
        foreach ($tables as $table) {
            $sqls = DatabaseFixer::sqlDataType($table);

            if (count($sqls) > 0) {
                $sql = implode(";\n", $sqls);
                $template = $app->addTemplate()
                    ->setTemplate('CApp/Administrator/Fixer/Database/DataType/Result')
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
        $sql = DatabaseFixer::sqlDataType($table);
        $db = c::db();
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
