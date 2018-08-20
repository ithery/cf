<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 8:06:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CGeo_Model_Address implements CGeo_Interface_LocationInterface {

    /**
     * @var Coordinates|null
     */
    private $coordinates;

    /**
     * @var CGeo_Model_Bounds|null
     */
    private $bounds;

    /**
     * @var string|int|null
     */
    private $streetNumber;

    /**
     * @var string|null
     */
    private $streetName;

    /**
     * @var string|null
     */
    private $subLocality;

    /**
     * @var string|null
     */
    private $locality;

    /**
     * @var string|null
     */
    private $postalCode;

    /**
     * @var CGeo_Model_AdminLevelCollection
     */
    private $adminLevels;

    /**
     * @var CGeo_Model_Country|null
     */
    private $country;

    /**
     * @var string|null
     */
    private $timezone;

    /**
     * @var string
     */
    private $providedBy;

    /**
     * @param string               $providedBy
     * @param CGeo_Model_AdminLevelCollection $adminLevels
     * @param CGeo_Model_Coordinates|null     $coordinates
     * @param CGeo_Model_Bounds|null          $bounds
     * @param string|null          $streetNumber
     * @param string|null          $streetName
     * @param string|null          $postalCode
     * @param string|null          $locality
     * @param string|null          $subLocality
     * @param CGeo_Model_Country|null         $country
     * @param string|null          $timezone
     */
    public function __construct($providedBy, CGeo_Model_AdminLevelCollection $adminLevels, CGeo_Model_Coordinates $coordinates = null, CGeo_Model_Bounds $bounds = null, $streetNumber = null, $streetName = null, $postalCode = null, $locality = null, $subLocality = null, CGeo_Model_Country $country = null, $timezone = null) {
        $this->providedBy = $providedBy;
        $this->adminLevels = $adminLevels;
        $this->coordinates = $coordinates;
        $this->bounds = $bounds;
        $this->streetNumber = $streetNumber;
        $this->streetName = $streetName;
        $this->postalCode = $postalCode;
        $this->locality = $locality;
        $this->subLocality = $subLocality;
        $this->country = $country;
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getProvidedBy() {
        return $this->providedBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getCoordinates() {
        return $this->coordinates;
    }

    /**
     * {@inheritdoc}
     */
    public function getBounds() {
        return $this->bounds;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreetNumber() {
        return $this->streetNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreetName() {
        return $this->streetName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocality() {
        return $this->locality;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostalCode() {
        return $this->postalCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubLocality() {
        return $this->subLocality;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminLevels() {
        return $this->adminLevels;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimezone() {
        return $this->timezone;
    }

    /**
     * Create an Address with an array. Useful for testing.
     *
     * @param array $data
     *
     * @return static
     */
    public static function createFromArray(array $data) {
        $defaults = [
            'providedBy' => 'n/a',
            'latitude' => null,
            'longitude' => null,
            'bounds' => [
                'south' => null,
                'west' => null,
                'north' => null,
                'east' => null,
            ],
            'streetNumber' => null,
            'streetName' => null,
            'locality' => null,
            'postalCode' => null,
            'subLocality' => null,
            'adminLevels' => [],
            'country' => null,
            'countryCode' => null,
            'timezone' => null,
        ];
        $data = array_merge($defaults, $data);
        $adminLevels = [];
        foreach ($data['adminLevels'] as $adminLevel) {
            if (empty($adminLevel['level'])) {
                continue;
            }
            $name = isset($adminLevel['name']) ? $adminLevel['name'] : isset($adminLevel['code']) ? $adminLevel['code'] : null;
            if (empty($name)) {
                continue;
            }
            $adminLevels[] = new CGeo_Model_AdminLevel($adminLevel['level'], $name, isset($adminLevel['code']) ? $adminLevel['code'] : null);
        }
        return new static(
                $data['providedBy'], new CGeo_Model_AdminLevelCollection($adminLevels), self::createCoordinates(
                        $data['latitude'], $data['longitude']
                ), self::createBounds(
                        $data['bounds']['south'], $data['bounds']['west'], $data['bounds']['north'], $data['bounds']['east']
                ), $data['streetNumber'], $data['streetName'], $data['postalCode'], $data['locality'], $data['subLocality'], self::createCountry($data['country'], $data['countryCode']), $data['timezone']
        );
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return Coordinates|null
     */
    private static function createCoordinates($latitude, $longitude) {
        if (null === $latitude || null === $longitude) {
            return null;
        }
        return new CGeo_Model_Coordinates($latitude, $longitude);
    }

    /**
     * @param string|null $name
     * @param string|null $code
     *
     * @return Country|null
     */
    private static function createCountry($name, $code) {
        if (null === $name && null === $code) {
            return null;
        }
        return new CGeo_Model_Country($name, $code);
    }

    /**
     * @param float $south
     * @param float $west
     * @param float $north
     *
     * @return CGeo_Model_Bounds|null
     */
    private static function createBounds($south, $west, $north, $east) {
        if (null === $south || null === $west || null === $north || null === $east) {
            return null;
        }
        return new CGeo_Model_Bounds($south, $west, $north, $east);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() {
        $adminLevels = [];
        foreach ($this->adminLevels as $adminLevel) {
            $adminLevels[$adminLevel->getLevel()] = [
                'name' => $adminLevel->getName(),
                'code' => $adminLevel->getCode(),
                'level' => $adminLevel->getLevel(),
            ];
        }
        $lat = null;
        $lon = null;
        if (null !== $coordinates = $this->getCoordinates()) {
            $lat = $coordinates->getLatitude();
            $lon = $coordinates->getLongitude();
        }
        $countryName = null;
        $countryCode = null;
        if (null !== $country = $this->getCountry()) {
            $countryName = $country->getName();
            $countryCode = $country->getCode();
        }
        $noBounds = [
            'south' => null,
            'west' => null,
            'north' => null,
            'east' => null,
        ];
        return [
            'providedBy' => $this->providedBy,
            'latitude' => $lat,
            'longitude' => $lon,
            'bounds' => null !== $this->bounds ? $this->bounds->toArray() : $noBounds,
            'streetNumber' => $this->streetNumber,
            'streetName' => $this->streetName,
            'postalCode' => $this->postalCode,
            'locality' => $this->locality,
            'subLocality' => $this->subLocality,
            'adminLevels' => $adminLevels,
            'country' => $countryName,
            'countryCode' => $countryCode,
            'timezone' => $this->timezone,
        ];
    }

}
