<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 1:43:03 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Application_Config_Editor {

    protected function getTitle() {
        return '';
    }

    protected function getConfigGroup() {
        return '';
    }

    protected function canEdit() {
        return false;
    }

    public function index() {
        $app = CApp::instance();
        $title = $this->getTitle();
        if ($title == '') {
            $title = ucfirst($this->getConfigGroup()) . ' Setting';
        }
        $app->title($title);

        $config = CConfig::instance($this->getConfigGroup());

        $table = $app->addTable();
        $table->setDataFromArray($config->getConfigData());
        $table->addColumn('key')->setLabel('Key');
        $table->addColumn('value')->setLabel('Value');
        $table->addColumn('file')->setLabel('File');
        $table->setApplyDataTable(false);


        echo $app->render();
    }

}
