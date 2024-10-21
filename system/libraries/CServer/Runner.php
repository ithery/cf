<?php
/**
 * @see CRunner
 */
class CServer_Runner {
    /**
     * @param array $options
     *
     * @return CServer_Runner_WkHtmlToPdf_Pdf
     */
    public static function wkHtmlToPdf($options = null) {
        return CServer_Runner_WkHtmlToPdf::createPdf($options);
    }

    /**
     * @param array $options
     *
     * @return CRunner_WkHtmlToPdf_Image
     */
    public static function wkHtmlToImage($options = null) {
        return CServer_Runner_WkHtmlToPdf::createImage($options);
    }

    /**
     * @param array $options
     *
     * @return CServer_Runner_FFMpeg
     */
    public static function ffmpeg($options = null) {
        return new CServer_Runner_FFMpeg($options);
    }
}
