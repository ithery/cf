<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
