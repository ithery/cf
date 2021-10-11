<?php

class Controller_Demo_Controls extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Controls');

        $widget = $app->addWidget()->setTitle('Controls Demo');
        $form = $widget->addForm();
        $form->addField()->setLabel('Text')->addControl(null, 'text')->setPlaceholder('Input here..');
        $form->addField()->setLabel('Select')->addControl(null, 'select')->setList(['apple' => 'Apple', 'orange' => 'Orange', 'grape' => 'Grape']);
        $form->addField()->setLabel('Email')->addControl(null, 'email')->setPlaceholder('Input Email..');
        $radioField = $form->addField()->setLabel('Radio');
        $radioField->addControl(null, 'radio')->setLabel('Radio 1')->setName('field-radio');
        $radioField->addControl(null, 'radio')->setLabel('Radio 2')->setName('field-radio');

        $form->addField()->setLabel('Label')->addControl(null, 'label')->setValue('Label Only');
        return $app;
    }
}
