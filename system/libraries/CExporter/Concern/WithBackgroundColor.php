<?php

use PhpOffice\PhpSpreadsheet\Style\Color;

interface CExporter_Concern_WithBackgroundColor {
    /**
     * @return string|array|Color
     */
    public function backgroundColor();
}
