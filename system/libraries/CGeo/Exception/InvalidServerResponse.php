<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * When the geocoder server returns something that we cannot process.
 */
final class CGeo_Exception_InvalidServerResponse extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
    /**
     * @param string $query
     * @param int    $code
     *
     * @return CGeo_Exception_InvalidServerResponse
     */
    public static function create($query, $code = 0) {
        return new self(sprintf('The geocoder server returned an invalid response (%d) for query "%s". We could not parse it.', $code, $query));
    }

    /**
     * @param string $query
     *
     * @return CGeo_Exception_InvalidServerResponse
     */
    public static function emptyResponse($query) {
        return new self(sprintf('The geocoder server returned an empty response for query "%s".', $query));
    }
}
