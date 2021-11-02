<?php
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;

interface CExporter_Concern_WithDrawings {
    /**
     * @return BaseDrawing|BaseDrawing[]
     */
    public function drawings();
}
