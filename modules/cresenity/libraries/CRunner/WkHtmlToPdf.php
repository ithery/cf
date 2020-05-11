<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CRunner_WkHtmlToPdf {

    /**
     * 
     * @param type $options
     * @return \CRunner_WkHtmlToPdf_Pdf
     */
    public static function createPdf($options = null) {
        $pdf = new CRunner_WkHtmlToPdf_Pdf($options);
        return $pdf;
    }

    /**
     * 
     * @param type $options
     * @return \CRunner_WkHtmlToPdf_Image
     */
    public static function createImage($options = null) {
        $pdf = new CRunner_WkHtmlToPdf_Image($options);
        return $pdf;
    }

}
