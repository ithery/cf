<?php

class Controller_Demo_Elements_Filemanager extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('File Manager');

        $widget = $app->addWidget()->setTitle('File Manager Demo');
        $widget->addDiv()->add(
            'Contoh CElement_Component_FileManager'
        );
        $widget->addBr();

        $fileManager = $widget->addFileManager('demoFileManager');
        $fileManager->setDisk('local')
            ->setRootPath('application/cresenity/default/data/demo/file-manager');

        return $app;
    }
}
