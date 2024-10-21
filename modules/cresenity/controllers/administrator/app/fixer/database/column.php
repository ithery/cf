<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 12:47:49 AM
 */

use CApp_Administrator_Fixer_Database as DatabaseFixer;

class Controller_Administrator_App_Fixer_Database_Column extends CApp_Administrator_Controller_User {
    public function index() {
        $app = CApp::instance();
        $app->title('Fix Database Table Engine');

        $action = $app->addAction()->setLabel('Check Table Column Problem')->addClass('btn-primary mb-3');
        $action->onClickListener()->addReloadHandler()->setTarget('data-type-result')->setUrl(curl::base() . 'administrator/app/fixer/database/column/check');

        $app->addDiv('data-type-result');

        return $app;
    }

    public function check() {
        $app = CApp::instance();

        $db = c::db();
        $schemaManager = $db->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        $haveChanged = false;
        foreach ($tables as $table) {
            $sqls = DatabaseFixer::sqlColumn($table);

            if (count($sqls) > 0) {
                $sql = implode(";\n", $sqls);

                $template = $app->addTemplate()
                    ->setTemplate('CApp/Administrator/Fixer/Database/Column/Result')
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

        return $app;
    }

    public function execute($table) {
        $sql = DatabaseFixer::sqlColumn($table);
        $db = c::db();
        $errCode = 0;
        $errMessage = '';

        try {
            $db->query($sql);
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        return CApp_Base::toJsonResponse($errCode, $errMessage);
    }
}
