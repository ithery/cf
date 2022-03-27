<?php

class CRedis_Formatter_Information {
    /**
     * Format memory information.
     *
     * @param array $info
     *
     * @return array
     */
    public static function memory($info) {
        if (isset($info['Memory'])) {
            $info['Memory']['used_memory'] = static::formatBytes($info['Memory']['used_memory']);
            $info['Memory']['used_memory_rss'] = static::formatBytes($info['Memory']['used_memory_rss']);
            $info['Memory']['used_memory_peak'] = static::formatBytes($info['Memory']['used_memory_peak']);
        } else {
            $info['used_memory'] = static::formatBytes($info['used_memory']);
            $info['used_memory_rss'] = static::formatBytes($info['used_memory_rss']);
            $info['used_memory_peak'] = static::formatBytes($info['used_memory_peak']);
        }

        return $info;
    }

    /**
     * Format commandstats information.
     *
     * @param array $info
     *
     * @return static
     */
    public static function commandstats($info) {
        $commands = c::collect($info['Commandstats'])->mapWithKeys(function ($value, $key) {
            preg_match('/calls=(\d+),usec=(\d+),usec_per_call=(.*)/', $value, $match);

            list($_, $calls, $usec, $usec_per_call) = $match;

            return [substr($key, 8) => compact('calls', 'usec', 'usec_per_call')];
        });

        return $commands;
    }

    /**
     * Format cpu information.
     *
     * @param array $info
     *
     * @return mixed
     */
    public static function cpu($info) {
        return $info['CPU'];
    }

    /**
     * Format clients information.
     *
     * @param array $info
     *
     * @return mixed
     */
    public static function clients($info) {
        return $info['Clients'];
    }

    /**
     * Format bytes to MB size.
     *
     * @param int $bytes
     * @param int $precision
     *
     * @return float
     */
    public static function formatBytes($bytes, $precision = 2) {
        $bytes = $bytes / (1024 * 1024);

        return round($bytes, $precision);
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments) {
        return $arguments[0];
    }
}
