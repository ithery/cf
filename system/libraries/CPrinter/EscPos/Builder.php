<?php

class CPrinter_EscPos_Builder {
    const ESC = "\x1b";

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
     * ASCII linefeed control character.
     */
    const LF = "\x0a";

    /**
     * ASCII form feed control character.
     */
    const FF = "\x0c";

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
}
