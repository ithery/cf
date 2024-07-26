<?php

class CReport_Pdf_FontManager {
    protected $fonts;

    private static $instance;

    /**
     * @return CReport_Pdf_FontManager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addFont($fontName, $fontPath) {
        $this->fonts[$fontName] = $fontPath;
    }

    public function getFontFile($fontName) {
        return carr::get($this->fonts, $fontName);
    }

    public function all() {
        return $this->fonts;
    }

    public function generateFont($fontPath, $outputPath) {
        require_once DOCROOT . 'system' . DS . 'vendor' . DS . 'TCPDF' . DS . 'include' . DS . 'tcpdf_fonts.php';
        $type = 'TrueTypeUnicode';
        $enc = '';
        $flags = 32;
        // $outputPath = '';
        $platid = 3;
        $encid = 1;
        $addcbbox = false;
        $link = false;
        $fontPath = realpath($fontPath);
        $a = new TCPDF();
        $fontname = TCPDF_FONTS::addTTFfont($fontPath, $type, $enc, $flags, $outputPath, $platid, $encid, $addcbbox, $link);
        if ($fontname === false) {
            throw new Exception("ERROR: can't add " . $fontPath . '');
        }
    }
}
