<?php
/**
 * @see https://github.com/mike42/escpos-php
 * @see CPrinter
 */
class CPrinter_EscPos {
    /**
     * ASCII null control character.
     */
    const NUL = "\x00";

    /**
     * ASCII linefeed control character.
     */
    const LF = "\x0a";

    /**
     * ASCII escape control character.
     */
    const ESC = "\x1b";

    /**
     * ASCII form separator control character.
     */
    const FS = "\x1c";

    /**
     * ASCII form feed control character.
     */
    const FF = "\x0c";

    /**
     * ASCII group separator control character.
     */
    const GS = "\x1d";

    /**
     * ASCII data link escape control character.
     */
    const DLE = "\x10";

    /**
     * ASCII end of transmission control character.
     */
    const EOT = "\x04";

    /**
     * Indicates UPC-A barcode when used with Printer::barcode.
     */
    const BARCODE_UPCA = 65;

    /**
     * Indicates UPC-E barcode when used with Printer::barcode.
     */
    const BARCODE_UPCE = 66;

    /**
     * Indicates JAN13 barcode when used with Printer::barcode.
     */
    const BARCODE_JAN13 = 67;

    /**
     * Indicates JAN8 barcode when used with Printer::barcode.
     */
    const BARCODE_JAN8 = 68;

    /**
     * Indicates CODE39 barcode when used with Printer::barcode.
     */
    const BARCODE_CODE39 = 69;

    /**
     * Indicates ITF barcode when used with Printer::barcode.
     */
    const BARCODE_ITF = 70;

    /**
     * Indicates CODABAR barcode when used with Printer::barcode.
     */
    const BARCODE_CODABAR = 71;

    /**
     * Indicates CODE93 barcode when used with Printer::barcode.
     */
    const BARCODE_CODE93 = 72;

    /**
     * Indicates CODE128 barcode when used with Printer::barcode.
     */
    const BARCODE_CODE128 = 73;

    /**
     * Indicates that HRI (human-readable interpretation) text should not be
     * printed, when used with Printer::setBarcodeTextPosition.
     */
    const BARCODE_TEXT_NONE = 0;

    /**
     * Indicates that HRI (human-readable interpretation) text should be printed
     * above a barcode, when used with Printer::setBarcodeTextPosition.
     */
    const BARCODE_TEXT_ABOVE = 1;

    /**
     * Indicates that HRI (human-readable interpretation) text should be printed
     * below a barcode, when used with Printer::setBarcodeTextPosition.
     */
    const BARCODE_TEXT_BELOW = 2;

    /**
     * Use the first color (usually black), when used with Printer::setColor.
     */
    const COLOR_1 = 0;

    /**
     * Use the second color (usually red or blue), when used with Printer::setColor.
     */
    const COLOR_2 = 1;

    /**
     * Make a full cut, when used with Printer::cut.
     */
    const CUT_FULL = 65;

    /**
     * Make a partial cut, when used with Printer::cut.
     */
    const CUT_PARTIAL = 66;

    /**
     * Use Font A, when used with Printer::setFont.
     */
    const FONT_A = 0;

    /**
     * Use Font B, when used with Printer::setFont.
     */
    const FONT_B = 1;

    /**
     * Use Font C, when used with Printer::setFont.
     */
    const FONT_C = 2;

    /**
     * Use default (high density) image size, when used with Printer::graphics,
     * Printer::bitImage or Printer::bitImageColumnFormat.
     */
    const IMG_DEFAULT = 0;

    /**
     * Use lower horizontal density for image printing, when used with Printer::graphics,
     * Printer::bitImage or Printer::bitImageColumnFormat.
     */
    const IMG_DOUBLE_WIDTH = 1;

    /**
     * Use lower vertical density for image printing, when used with Printer::graphics,
     * Printer::bitImage or Printer::bitImageColumnFormat.
     */
    const IMG_DOUBLE_HEIGHT = 2;

    /**
     * Align text to the left, when used with Printer::setJustification.
     */
    const JUSTIFY_LEFT = 0;

    /**
     * Center text, when used with Printer::setJustification.
     */
    const JUSTIFY_CENTER = 1;

    /**
     * Align text to the right, when used with Printer::setJustification.
     */
    const JUSTIFY_RIGHT = 2;

    /**
     * Use Font A, when used with Printer::selectPrintMode.
     */
    const MODE_FONT_A = 0;

    /**
     * Use Font B, when used with Printer::selectPrintMode.
     */
    const MODE_FONT_B = 1;

    /**
     * Use text emphasis, when used with Printer::selectPrintMode.
     */
    const MODE_EMPHASIZED = 8;

    /**
     * Use double height text, when used with Printer::selectPrintMode.
     */
    const MODE_DOUBLE_HEIGHT = 16;

    /**
     * Use double width text, when used with Printer::selectPrintMode.
     */
    const MODE_DOUBLE_WIDTH = 32;

    /**
     * Underline text, when used with Printer::selectPrintMode.
     */
    const MODE_UNDERLINE = 128;

    /**
     * Indicates standard PDF417 code.
     */
    const PDF417_STANDARD = 0;

    /**
     * Indicates truncated PDF417 code.
     */
    const PDF417_TRUNCATED = 1;

    /**
     * Indicates error correction level L when used with Printer::qrCode.
     */
    const QR_ECLEVEL_L = 0;

    /**
     * Indicates error correction level M when used with Printer::qrCode.
     */
    const QR_ECLEVEL_M = 1;

    /**
     * Indicates error correction level Q when used with Printer::qrCode.
     */
    const QR_ECLEVEL_Q = 2;

    /**
     * Indicates error correction level H when used with Printer::qrCode.
     */
    const QR_ECLEVEL_H = 3;

    /**
     * Indicates QR model 1 when used with Printer::qrCode.
     */
    const QR_MODEL_1 = 1;

    /**
     * Indicates QR model 2 when used with Printer::qrCode.
     */
    const QR_MODEL_2 = 2;

    /**
     * Indicates micro QR code when used with Printer::qrCode.
     */
    const QR_MICRO = 3;

    /**
     * Indicates a request for printer status when used with
     * Printer::getPrinterStatus (experimental).
     */
    const STATUS_PRINTER = 1;

    /**
     * Indicates a request for printer offline cause when used with
     * Printer::getPrinterStatus (experimental).
     */
    const STATUS_OFFLINE_CAUSE = 2;

    /**
     * Indicates a request for error cause when used with Printer::getPrinterStatus
     * (experimental).
     */
    const STATUS_ERROR_CAUSE = 3;

    /**
     * Indicates a request for error cause when used with Printer::getPrinterStatus
     * (experimental).
     */
    const STATUS_PAPER_ROLL = 4;

    /**
     * Indicates a request for ink A status when used with Printer::getPrinterStatus
     * (experimental).
     */
    const STATUS_INK_A = 7;

    /**
     * Indicates a request for ink B status when used with Printer::getPrinterStatus
     * (experimental).
     */
    const STATUS_INK_B = 6;

    /**
     * Indicates a request for peeler status when used with Printer::getPrinterStatus
     * (experimental).
     */
    const STATUS_PEELER = 8;

    /**
     * Indicates no underline when used with Printer::setUnderline.
     */
    const UNDERLINE_NONE = 0;

    /**
     * Indicates single underline when used with Printer::setUnderline.
     */
    const UNDERLINE_SINGLE = 1;

    /**
     * Indicates double underline when used with Printer::setUnderline.
     */
    const UNDERLINE_DOUBLE = 2;

    /**
     * @var CPrinter_EscPos
     */
    private static $instance;

    /**
     * @return CPrinter_EscPos
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CPrinter_EscPos();
        }

        return self::$instance;
    }

    public function getProfileNames() {
        return CPrinter_EscPos_CapabilityProfile::getProfileNames();
    }

    public function createBuilder() {
        return new CPrinter_EscPos_Builder();
    }

    public function renderToHtml($data) {
        $renderer = new CPrinter_EscPos_Renderer_HtmlRenderer($data);

        return $renderer->render();
    }
}
