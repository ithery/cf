<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Formats data to be outputed as string.
 */
interface CDebug_Contract_DataFormatterInterface {
    /**
     * Transforms a PHP variable to a string representation.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function formatVar($data);

    /**
     * Transforms a duration in seconds in a readable string.
     *
     * @param float $seconds
     *
     * @return string
     */
    public function formatDuration($seconds);

    /**
     * Transforms a size in bytes to a human readable string.
     *
     * @param string $size
     * @param int    $precision
     *
     * @return string
     */
    public function formatBytes($size, $precision = 2);
}
