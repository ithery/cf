<?php

use PhpOffice\PhpSpreadsheet\Chart\Chart;

interface CExporter_Concern_WithCharts {
    /**
     * @return Chart|Chart[]
     */
    public function charts();
}
