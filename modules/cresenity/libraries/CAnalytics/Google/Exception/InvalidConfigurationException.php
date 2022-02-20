<?php

class CAnalytics_Google_Exception_InvalidConfigurationException extends Exception {
    /**
     * @return static
     */
    public static function viewIdNotSpecified() {
        return new static('There was no view ID specified. You must provide a valid view ID to execute queries on Google Analytics.');
    }

    /**
     * @param string $path
     *
     * @return static
     */
    public static function credentialsJsonDoesNotExist($path) {
        return new static("Could not find a credentials file at `{$path}`.");
    }
}
