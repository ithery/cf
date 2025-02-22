<?php
/**
 * Interface passed to Escpos class for receiving print data. Print connectors
 * are responsible for transporting this to the actual printer.
 */
interface CPrinter_EscPos_Contract_PrintConnectorInterface {
    /**
     * Print connectors should cause a NOTICE if they are deconstructed
     * when they have not been finalized.
     */
    public function __destruct();

    /**
     * Finish using this print connector (close file, socket, send
     * accumulated output, etc).
     */
    public function finalize();

    /**
     * Read data from the printer.
     *
     * @param string $len length of data to read
     *
     * @return string data read from the printer, or false where reading is not possible
     */
    public function read($len);

    /**
     * Write data to the print connector.
     *
     * @param string $data The data to write
     */
    public function write($data);
}
