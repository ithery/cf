<?php

final class CGeo_Model_AddressBuilder {
    /**
     * @var string
     */
    private $providedBy;

    /**
     * @var null|CGeo_Model_Coordinates
     */
    private $coordinates;

    /**
     * @var null|CGeo_Model_Bounds
     */
    private $bounds;

    /**
     * @var null|string
     */
    private $streetNumber;

    /**
     * @var null|string
     */
    private $streetName;

    /**
     * @var null|string
     */
    private $locality;

    /**
     * @var null|string
     */
    private $postalCode;

    /**
     * @var null|string
     */
    private $subLocality;

    /**
     * @var array
     */
    private $adminLevels = [];

    /**
     * @var null|string
     */
    private $country;

    /**
     * @var null|string
     */
    private $countryCode;

    /**
     * @var null|string
     */
    private $timezone;

    /**
     * A storage for extra parameters.
     *
     * @var array
     */
    private $data = [];

    /**
     * @param string $providedBy
     */
    public function __construct(string $providedBy) {
        $this->providedBy = $providedBy;
    }

    /**
     * @param string $class
     *
     * @return CGeo_Model_Address
     */
    public function build($class = CGeo_Model_Address::class) {
        if (!is_a($class, CGeo_Model_Address::class, true)) {
            throw new CGeo_Exception_LogicException('First parameter to LocationBuilder::build must be a class name extending Geocoder\Model\Address');
        }

        $country = null;
        if ((null !== $this->country && '' !== $this->country) || (null !== $this->countryCode && '' !== $this->countryCode)) {
            $country = new CGeo_Model_Country($this->country, $this->countryCode);
        }

        return new $class(
            $this->providedBy,
            new CGeo_Model_AdminLevelCollection($this->adminLevels),
            $this->coordinates,
            $this->bounds,
            $this->streetNumber,
            $this->streetName,
            $this->postalCode,
            $this->locality,
            $this->subLocality,
            $country,
            $this->timezone
        );
    }

    /**
     * @param float $south
     * @param float $west
     * @param float $north
     * @param float $east
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setBounds($south, $west, $north, $east) {
        try {
            $this->bounds = new CGeo_Model_Bounds($south, $west, $north, $east);
        } catch (CGeo_Exception_InvalidArgument $e) {
            $this->bounds = null;
        }

        return $this;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setCoordinates($latitude, $longitude) {
        try {
            $this->coordinates = new CGeo_Model_Coordinates($latitude, $longitude);
        } catch (CGeo_Exception_InvalidArgument $e) {
            $this->coordinates = null;
        }

        return $this;
    }

    /**
     * @param int         $level
     * @param string      $name
     * @param null|string $code
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function addAdminLevel(int $level, string $name, string $code = null) {
        $this->adminLevels[] = new CGeo_Model_AdminLevel($level, $name, $code);

        return $this;
    }

    /**
     * @param null|string $streetNumber
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setStreetNumber($streetNumber) {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     * @param null|string $streetName
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setStreetName($streetName) {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * @param null|string $locality
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setLocality($locality) {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @param null|string $postalCode
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @param null|string $subLocality
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setSubLocality($subLocality) {
        $this->subLocality = $subLocality;

        return $this;
    }

    /**
     * @param array $adminLevels
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setAdminLevels($adminLevels) {
        $this->adminLevels = $adminLevels;

        return $this;
    }

    /**
     * @param null|string $country
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setCountry($country) {
        $this->country = $country;

        return $this;
    }

    /**
     * @param null|string $countryCode
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setCountryCode($countryCode) {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @param null|string $timezone
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setTimezone($timezone) {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return CGeo_Model_AddressBuilder
     */
    public function setValue($name, $value) {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * @param string     $name
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getValue($name, $default = null) {
        if ($this->hasValue($name)) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasValue($name) {
        return array_key_exists($name, $this->data);
    }
}
