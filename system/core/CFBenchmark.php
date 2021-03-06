<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Simple benchmarking.
 */
final class CFBenchmark {
    // Benchmark timestamps
    private static $marks;

    private static $onStopCallback;

    /**
     * Set a benchmark start point.
     *
     * @param string $name benchmark name
     *
     * @return void
     */
    public static function start($name) {
        if (!isset(self::$marks[$name])) {
            self::$marks[$name] = [];
        }

        $mark = [
            'start' => microtime(true),
            'stop' => false,
            'memory_start' => self::memoryUsage(),
            'memory_stop' => false
        ];

        array_unshift(self::$marks[$name], $mark);
    }

    /**
     * Set a benchmark stop point.
     *
     * @param string $name benchmark name
     *
     * @return void
     */
    public static function stop($name) {
        if (isset(self::$marks[$name]) and self::$marks[$name][0]['stop'] === false) {
            self::$marks[$name][0]['stop'] = microtime(true);
            self::$marks[$name][0]['memory_stop'] = self::memoryUsage();

            if (static::$onStopCallback != null) {
                $callback = static::$onStopCallback;
                $callback($name, static::$marks[$name][0]);
            }
        }
    }

    /**
     * Get the elapsed time between a start and stop.
     *
     * @param string $name     benchmark name, TRUE for all
     * @param int    $decimals number of decimal places to count to
     *
     * @return array
     */
    public static function get($name, $decimals = 4) {
        if ($name === true) {
            $times = [];
            $names = array_keys(self::$marks);

            foreach ($names as $name) {
                // Get each mark recursively
                $times[$name] = self::get($name, $decimals);
            }

            // Return the array
            return $times;
        }

        if (!isset(self::$marks[$name])) {
            return false;
        }

        if (self::$marks[$name][0]['stop'] === false) {
            // Stop the benchmark to prevent mis-matched results
            self::stop($name);
        }

        // Return a string version of the time between the start and stop points
        // Properly reading a float requires using number_format or sprintf
        $time = $memory = 0;
        for ($i = 0; $i < count(self::$marks[$name]); $i++) {
            $time += self::$marks[$name][$i]['stop'] - self::$marks[$name][$i]['start'];
            $memory += self::$marks[$name][$i]['memory_stop'] - self::$marks[$name][$i]['memory_start'];
        }

        return [
            'time' => number_format($time, $decimals),
            'memory' => $memory,
            'count' => count(self::$marks[$name])
        ];
    }

    /**
     * Returns the current memory usage. This is only possible if the
     * memory_get_usage function is supported in PHP.
     *
     * @return int
     */
    private static function memoryUsage() {
        static $func;

        if ($func === null) {
            // Test if memory usage can be seen
            $func = function_exists('memory_get_usage');
        }

        return $func ? memory_get_usage() : 0;
    }

    public static function all() {
        return static::$marks;
    }

    public static function completed() {
        $completed = [];
        foreach (static::all() as $key => $marks) {
            if ($marks[0]['stop'] !== false) {
                $completed[$key] = $marks[0];
            }
        }

        return $completed;
    }

    public static function onStopCallback($callback) {
        static::$onStopCallback = $callback;
    }
}

// End Benchmark
