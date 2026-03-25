<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * A location is a single result from a Geocoder.
 */
interface CGeo_Interface_LocationInterface {
    /**
     * Will always return the coordinates value object.
     *
     * @return null|CGeo_Model_Coordinates
     */
    public function getCoordinates();

    /**
     * Returns the bounds value object.
     *
     * @return null|CGeo_Model_Bounds
     */
    public function getBounds();

    /**
     * Returns the street number value.
     *
     * @return null|string|int
     */
    public function getStreetNumber();

    /**
     * Returns the street name value.
     *
     * @return null|string
     */
    public function getStreetName();

    /**
     * Returns the city or locality value.
     *
     * @return null|string
     */
    public function getLocality();

    /**
     * Returns the postal code or zipcode value.
     *
     * @return null|string
     */
    public function getPostalCode();

    /**
     * Returns the locality district, or
     * sublocality, or neighborhood.
     *
     * @return null|string
     */
    public function getSubLocality();

    /**
     * Returns the administrative levels.
     *
     * This method MUST NOT return null.
     *
     * @return CGeo_Model_AdminLevelCollection
     */
    public function getAdminLevels();

    /**
     * Returns the country value object.
     *
     * @return null|CGeo_Model_Country
     */
    public function getCountry();

    /**
     * Returns the timezone for the Location. The timezone MUST be in the list of supported timezones.
     *
     * {@link http://php.net/manual/en/timezones.php}
     *
     * @return null|string
     */
    public function getTimezone();

    /**
     * Returns an array with data indexed by name.
     *
     * @return array
     */
    public function toArray();

    /**
     * The name of the provider that created this Location.
     *
     * @return string
     */
    public function getProvidedBy();
}
