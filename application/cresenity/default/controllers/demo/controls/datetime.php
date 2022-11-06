<?php

class Controller_Demo_Controls_Datetime extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $periodDefault = CPeriod::months(1);

        $dateStart = $periodDefault->startDate;

        $dateEnd = $periodDefault->endDate;

        $app->setTitle('Date Time');
        $widget = $app->addWidget();
        $widget->setIcon(' ti ti-filter')->setTitle('Modal')->addClass('mb-3');
        $form = $widget->addForm();
        $divRow = $form->addDiv()->addClass('row');
        $divRow->addDiv()->addClass('col-md-6')->addField()
            ->setLabel('Start Date')
            ->addDateTimeModalControl('date-modal-start')
            ->setValue($dateStart);
        $divRow->addDiv()->addClass('col-md-6')->addField()
            ->setLabel('End Date')
            ->addDateTimeModalControl('date-modal-end')
            ->setValue($dateEnd);

        $widget = $app->addWidget();
        $widget->setIcon(' ti ti-filter')->setTitle('Material')->addClass('mb-3');
        $form = $widget->addForm();
        $divRow = $form->addDiv()->addClass('row');
        $divRow->addDiv()->addClass('col-md-6')->addField()
            ->setLabel('Start Date')
            ->addDateTimeMaterialControl('date-material-start')
            ->setValue($dateStart);
        $divRow->addDiv()->addClass('col-md-6')->addField()
            ->setLabel('End Date')
            ->addDateTimeMaterialControl('date-material-end')
            ->setValue($dateEnd);

        return $app;
    }
}
