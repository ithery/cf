<?php

interface CExporter_Concern_WithMapping {
    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row);
}
