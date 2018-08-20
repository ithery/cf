<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:10:22 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * A location is a single result from a Geocoder.
 *
 */
interface CGeo_Interface_LocationInterface {

    /**
     * Will always return the coordinates value object.
     *
     * @return CGeo_Model_Coordinates|null
     */
    public function getCoordinates();

    /**
     * Returns the bounds value object.
     *
     * @return CGeo_Model_Bounds|null
     */
    public function getBounds();

    /**
     * Returns the street number value.
     *
     * @return string|int|null
     */
    public function getStreetNumber();

    /**
     * Returns the street name value.
     *
     * @return string|null
     */
    public function getStreetName();

    /**
     * Returns the city or locality value.
     *
     * @return string|null
     */
    public function getLocality();

    /**
     * Returns the postal code or zipcode value.
     *
     * @return string|null
     */
    public function getPostalCode();

    /**
     * Returns the locality district, or
     * sublocality, or neighborhood.
     *
     * @return string|null
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
     * @return CGeo_Model_Country|null
     */
    public function getCountry();

    /**
     * Returns the timezone for the Location. The timezone MUST be in the list of supported timezones.
     *
     * {@link http://php.net/manual/en/timezones.php}
     *
     * @return string|null
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
