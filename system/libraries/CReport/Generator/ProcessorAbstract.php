<?php

abstract class CReport_Generator_ProcessorAbstract {
    protected $report;

    protected $currentY;

    protected $pageHeight;

    protected $pageWidth;

    protected $topMargin;

    protected $leftMargin;

    protected $rightMargin;

    protected $bottomMargin;

    protected $currentX;

    public function __construct(CReport_Builder_Report $report) {
        $this->report = $report;
        $this->pageHeight = $report->getOrientation() == CReport::ORIENTATION_LANDSCAPE ? $report->getPageWidth() : $report->getPageHeight();
        $this->pageWidth = $report->getOrientation() == CReport::ORIENTATION_LANDSCAPE ? $report->getPageHeight() : $report->getPageWidth();

        $this->topMargin = $report->getTopMargin();
        $this->bottomMargin = $report->getBottomMargin();
        $this->leftMargin = $report->getLeftMargin();
        $this->rightMargin = $report->getRightMargin();
        $this->currentY = $this->topMargin;
        $this->currentX = $this->leftMargin;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function cell(array $options);

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function cellHeight(array $options);

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function image(array $options);

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function line(array $options);

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function rectangle(array $options);

    /**
     * @param float $height
     *
     * @return float
     */
    abstract public function addY($height);

    /**
     * @return float
     */
    abstract public function resetY();

    /**
     * @return void
     */
    abstract public function resetTextColor();

    /**
     * @param float $y
     *
     * @return float
     */
    abstract public function setY($y);

    /**
     * @return float
     */
    abstract public function getY();

    /**
     * @return mixed
     */
    abstract public function getOutput();

    /**
     * @param CReport_Generator $generator
     * @param float             $height
     *
     * @return float
     */
    abstract public function preventYOverflow(CReport_Generator $generator, $height);

    /**
     * @param int $page
     *
     * @return void
     */
    abstract public function setPage($page);

    /**
     * @param string $fontName
     * @param string $fontPath
     *
     * @return void
     */
    abstract public function addFont($fontName, $fontPath);

    /**
     * @param string $content
     *
     * @return void
     */
    abstract public function raw($content);
}
