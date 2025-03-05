<?php
/**
 * @deprecated since 1.6 use CServer_Runner
 */
class CRunner {
    /**
     * @param array $options
     *
     * @return CRunner_WkHtmlToPdf_Pdf
     */
    public static function wkHtmlToPdf($options = null) {
        return CRunner_WkHtmlToPdf::createPdf($options);
    }

    /**
     * @param array $options
     *
     * @return CRunner_WkHtmlToPdf_Image
     */
    public static function wkHtmlToImage($options = null) {
        return CRunner_WkHtmlToPdf::createImage($options);
    }

    /**
     * @param array $options
     *
     * @return CRunner_FFMpeg
     */
    public static function ffmpeg($options = null) {
        return new CRunner_FFMpeg($options);
    }

    /**
     * @param array $options
     *
     * @return CRunner_Teserract
     */
    public static function teserract($options = null) {
        return new CRunner_Teserract($options);
    }
}
