<?php

class CReport_Builder {
    use CTrait_ForwardsCalls;
    /**
     * @var CReport_Builder_Report
     */
    protected $report;

    public function __construct() {
        $this->report = new CReport_Builder_Report();
    }

    public function setPaperSize($size) {
        $size = cstr::upper($size);
        $sizes = carr::get(CReport_Paper::$pageFormats, $size);

        if ($sizes == null) {
            $sizes = CReport_Paper::$pageFormats['A4'];
        }
        $this->report->setPageWidth($sizes[0]);
        $this->report->setPageHeight($sizes[1]);

        return $this;
    }

    public function setOrientation($orientation) {
        $this->report->setOrientation(cstr::lower($orientation) == CReport_Paper::ORIENTATION_LANDSCAPE ? CReport_Paper::ORIENTATION_LANDSCAPE : CReport_Paper::ORIENTATION_PORTRAIT);
    }

    /**
     * @return CReport_Pdf_Adapter_TCPDF
     */
    public function getPdf() {
        $jrxml = $this->report->toJrXml();

        $report = CReport::jasper($jrxml, []);

        $pdf = $report->getPdf();

        return $pdf;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @see CModel
     *
     * @return void
     */
    public function __call($name, $arguments) {
    }
}
