<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_JobName {
    /**
     * Parse the given job name into a class / method array.
     *
     * @param string $job
     *
     * @return array
     */
    public static function parse($job) {
        return cstr::parseCallback($job, 'fire');
    }

    /**
     * Get the resolved name of the queued job class.
     *
     * @param string $name
     * @param array  $payload
     *
     * @return string
     */
    public static function resolve($name, $payload) {
        if (!empty($payload['displayName'])) {
            return $payload['displayName'];
        }

        return $name;
    }
}
