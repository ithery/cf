<?php

class Controller_Demo_Controls_Summernote extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Summernote');

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Default Toolbar');
        $div->addSummerNoteControl()->setValue('<p>Hello <b>World</b>!</p>');

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Standard Toolbar');
        $div->addSummerNoteControl()->setToolbarType('standard')->setPlaceholder('Write something...');

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Text Only Toolbar');
        $div->addSummerNoteControl()->setToolbarType('text-only');

        return $app;
    }
}
