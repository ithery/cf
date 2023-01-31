<?php

use PhpOffice\PhpSpreadsheet\Style\Style;

interface CExporter_Concern_WithDefaultStyles {
    /**
     * @return array|void
     */
    public function defaultStyles(Style $defaultStyle);
}
