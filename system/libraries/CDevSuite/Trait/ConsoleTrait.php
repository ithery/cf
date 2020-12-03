<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

trait CDevSuite_Trait_ConsoleTrait {
    /**
     * Output the given text to the console.
     *
     * @param string $output
     * @return void
     */

    /**
     * The output interface implementation.
     *
     * @var CConsole_OutputStyle
     */
    protected static $outputStyle;

    /**
     * Output the given text to the console.
     *
     * @param string $output
     * @return void
     */
    public static function info($output) {
        //static::getOutputStyle()->text($output);
        static::output('<info>' . $output . '</info>');
    }

    /**
     * Output the given text to the console.
     *
     * @param string $output
     * @return void
     */
    public static function warning($output) {
        static::getOutputStyle()->warning($output);
        //static::error($output);
    }

    /**
     * Output the given text to the console.
     *
     * @param string $output
     * @return void
     */
    public static function error($output) {
        static::getOutputStyle()->error($output);
        //static::output('<fg=red>' . $output . '</>');
    }

    /**
     * Formats a success result bar.
     *
     * @param string|array $message
     */
    public static function success($message) {
        static::getOutputStyle()->success($output);
    }

    /**
     * Output a table to the console.
     *
     * @param array $headers
     * @param array $rows
     * @return void
     */
    public static function table(array $headers = [], array $rows = []) {
        static::getOutputStyle()->table($headers, $rows);
    }

    /**
     * Output the given text to the console.
     *
     * @param string $output
     * @return void
     */
    public static function output($output) {
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing') {
            return;
        }
        static::getOutputStyle()->writeln($output);
    }

    /**
     * Confirm the given text to the console.
     *
     * @param string $output
     * @param bool $default
     * @return void
     */
    public static function confirm($output, $default = true) {
        static::getOutputStyle()->confirm($output, $default);
    }

    /**
     * 
     * @return \CConsole_OutputStyle
     */
    protected static function getOutputStyle() {
        if (static::$outputStyle == null) {
            static::$outputStyle = new CConsole_OutputStyle(new Symfony\Component\Console\Input\ArgvInput, new Symfony\Component\Console\Output\ConsoleOutput);
        }
        return static::$outputStyle;
    }

    /**
     * Starts the progress output.
     *
     * @param int $max Maximum steps (0 if unknown)
     */
    public static function progressStart($max = 0) {
        static::getOutputStyle()->progressStart($max);
    }

    /**
     * Advances the progress output X steps.
     *
     * @param int $step Number of steps to advance
     */
    public static function progressAdvance($step = 1) {
        static::getOutputStyle()->progressAdvance($step);
    }

    /**
     * Finishes the progress output.
     */
    public static function progressFinish() {
        static::getOutputStyle()->progressFinish();
    }

}
