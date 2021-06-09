<?php

class CRunner_WkHtmlToPdf {
    /**
     * @param type $options
     *
     * @return \CRunner_WkHtmlToPdf_Pdf
     */
    public static function createPdf($options = null) {
        $pdf = new CRunner_WkHtmlToPdf_Pdf($options);
        return $pdf;
    }

    /**
     * @param type $options
     *
     * @return \CRunner_WkHtmlToPdf_Image
     */
    public static function createImage($options = null) {
        $pdf = new CRunner_WkHtmlToPdf_Image($options);
        return $pdf;
    }
}
