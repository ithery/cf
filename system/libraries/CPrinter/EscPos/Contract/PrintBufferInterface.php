<?php

/**
 * Print buffers manage newlines and character encoding for the target printer.
 * They are used as a swappable component: text or image-based output.
 *
 * - Text output (EscposPrintBuffer) is the fast default, and is recommended for
 *   most people, as the text output can be more directly manipulated by ESC/POS
 *   commands.
 * - Image output (ImagePrintBuffer) is designed to accept more encodings than the
 *   physical printer supports, by rendering the text to small images on-the-fly.
 *   This takes a lot more CPU than sending text, but is necessary for some users.
 * - If your use case fits outside these, then a further speed/flexibility trade-off
 *   can be made by printing directly from generated HTML or PDF.
 */
interface CPrinter_EscPos_Contract_PrintBufferInterface {
    /**
     * Cause the buffer to send any partial input and wait on a newline.
     * If the printer is already on a new line, this does nothing.
     */
    public function flush();

    /**
     * Used by Escpos to check if a printer is set.
     */
    public function getPrinter();

    /**
     * Used by Escpos to hook up one-to-one link between buffers and printers.
     *
     * @param null|CPrinter_EscPos_Printer $printer New printer
     */
    public function setPrinter(CPrinter_EscPos_Printer $printer = null);

    /**
     * Accept UTF-8 text for printing.
     *
     * @param string $text Text to print
     */
    public function writeText(string $text);

    /**
     * Accept 8-bit text in the current encoding and add it to the buffer.
     *
     * @param string $text text to print, already the target encoding
     */
    public function writeTextRaw(string $text);
}
