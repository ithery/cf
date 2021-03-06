<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:34:00 PM
 */
interface CGeo_Interface_GeocoderInterface extends CGeo_Interface_ProviderInterface {
    /**
     * Version of this package.
     */
    const MAJOR_VERSION = 4;

    const VERSION = '4.0';

    /**
     * The default result limit.
     */
    const DEFAULT_RESULT_LIMIT = 5;

    /**
     * Geocodes a given value.
     *
     * @param string $value
     *
     * @throws CGeo_Interface_ExceptionInterface
     *
     * @return CGeo_Interface_CollectionInterface
     */
    public function geocode($value);

    /**
     * Reverses geocode given latitude and longitude values.
     *
     * @param float $latitude
     * @param float $longitude
     *
     * @throws CGeo_Interface_ExceptionInterface
     *
     * @return CGeo_Interface_CollectionInterface
     */
    public function reverse($latitude, $longitude);
}
