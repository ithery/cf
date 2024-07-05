<?php

class CReport_Jasper {
    protected $report;

    public function __construct($jrxml, $param = []) {
        $this->report = new CReport_Jasper_Report($jrxml, $param);
    }

    public function exportPdf() {
        CReport_Jasper_Instructions::prepare($this->report);
        $this->report->generate();
        $this->report->out();
        $pdf = CReport_Jasper_Instructions::get();
        // cdbg::dd($pdf);
        $pdf->Output('report.pdf', 'I');
    }
}
