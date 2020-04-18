<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 12:47:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Administrator_Fixer_Database as DatabaseFixer;

class Controller_Administrator_App_Fixer_Table_Column extends CApp_Administrator_Controller_User {

    public function index($table) {

        $app = CApp::instance();


        $haveChanged = false;

        $sql = DatabaseFixer::sqlColumn($table);

        if (strlen($sql) > 0) {
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

        if (!$haveChanged) {
            $app->addAlert()->setType('success')->add('No Problem Found');
        }

        echo $app->render();
    }

    public function execute($table) {
        $sql = DatabaseFixer::sqlColumn($table);
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
