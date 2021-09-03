<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 4:44:23 PM
 */
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class CDebug_DataFormatter implements CDebug_Interface_DataFormatterInterface {
    /**
     * DataFormatter constructor.
     */
    public function __construct() {
        $this->cloner = new VarCloner();
        $this->dumper = new CliDumper();
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function formatVar($data) {
        $output = '';
        $this->dumper->dump(
            $this->cloner->cloneVar($data),
            function ($line, $depth) use (&$output) {
                // A negative depth means "end of dump"
                if ($depth >= 0) {
                    // Adds a two spaces indentation to the line
                    $output .= str_repeat('  ', $depth) . $line . "\n";
                }
            }
        );
        return trim($output);
    }

    /**
     * @param float $seconds
     *
     * @return string
     */
    public function formatDuration($seconds) {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        } elseif ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }
        return round($seconds, 2) . 's';
    }

    /**
     * @param string $size
     * @param int    $precision
     *
     * @return string
     */
    public function formatBytes($size, $precision = 2) {
        if ($size === 0 || $size === null) {
            return '0B';
        }
        $sign = $size < 0 ? '-' : '';
        $size = abs($size);
        $base = log($size) / log(1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        return $sign . round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
}
