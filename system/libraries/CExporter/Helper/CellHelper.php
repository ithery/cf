<?php

class CExporter_Helper_CellHelper {
    /**
     * @param string $coordinate
     *
     * @return string
     */
    public static function getColumnFromCoordinate($coordinate) {
        return preg_replace('/[0-9]/', '', $coordinate);
    }
}
