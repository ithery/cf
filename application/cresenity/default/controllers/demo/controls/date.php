<?php

class Controller_Demo_Controls_Date extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $date = c::now();

        $app->setTitle('Date');
        $widget = $app->addWidget();
        $widget->setIcon(' ti ti-filter')->setTitle('Date')->addClass('mb-3');
        $form = $widget->addForm();
        $divRow = $form->addDiv()->addClass('row');
        $divRow->addDiv()->addClass('col-md-6')->addField()
            ->setLabel('Date')
            ->addDateControl('date-normal')
            ->setValue($date);

        $widget = $app->addWidget();
        $widget->setIcon(' ti ti-filter')->setTitle('Date With Max Date')->addClass('mb-3');
        $form = $widget->addForm();
        $divRow = $form->addDiv()->addClass('row');
        $divRow->addDiv()->addClass('col-md-6')->addField()
            ->setLabel('Date')
            ->addDateControl('date-maxdate')
            ->setValue($date)
            ->setEndDate(c::now()->addDays(10));

        return $app;
    }
}
