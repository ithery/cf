<?php

class Controller_Demo_Report_Simple extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        $app->title('Report - Simple');
        $app->addDiv()->addClass('mb-2')
            ->addAction()->setLabel('Download Excel')->addClass('btn btn-sm btn-outline-success')
            ->setLink(c::url('demo/report/simple/excel'));
        $app->addIframe()->setSrc(c::url('demo/report/simple/pdf'))
            ->customCss('width', '100%')
            ->customCss('height', '800px');

        return $app;
    }

    public function pdf() {
        return $this->buildReport()->downloadPdf();
    }

    public function excel() {
        return $this->buildReport()->downloadExcel('simple-report.xlsx');
    }

    /**
     * @return CReport_Builder
     */
    private function buildReport() {
        $report = CReport::builder();
        $xml = c::view('demo.page.report.simple-jrxml')->render();
        $report->fromXml($xml);
        $report->setDataFromModel(Cresenity\Demo\Model\Country::class, function (CModel_Query $query) {
            $query->orderBy('continent');
        });

        return $report;
    }
}
