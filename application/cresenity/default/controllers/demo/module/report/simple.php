<?php

class Controller_Demo_Module_Report_Simple extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        $app->title('Report - Simple');
        $app->addIframe()->setSrc(c::url('demo/module/report/simple/pdf'))
            ->customCss('width', '100%')
            ->customCss('height', '800px');

        return $app;
    }

    public function pdf() {
        $report = CReport::builder();
        $xml = c::view('demo.page.module.report.simple-jrxml')->render();
        $report->fromXml($xml);
        $report->setDataFromModel(Cresenity\Demo\Model\Country::class, function (CModel_Query $query) {
            $query->orderBy('continent');
        });

        return $report->downloadPdf();
    }
}
