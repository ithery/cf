<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 9:06:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CGeo_Query_ReverseQuery implements CGeo_Interface_QueryInterface {

    /**
     * @var CGeo_Model_Coordinates
     */
    private $coordinates;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * @var int
     */
    private $limit = CGeo_Interface_GeocoderInterface::DEFAULT_RESULT_LIMIT;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param CGeo_Model_Coordinates $coordinates
     */
    private function __construct(CGeo_Model_Coordinates $coordinates) {
        $this->coordinates = $coordinates;
    }

    /**
     * @param CGeo_Model_Coordinates $coordinates
     *
     * @return CGeo_Query_ReverseQuery
     */
    public static function create(CGeo_Model_Coordinates $coordinates) {
        return new self($coordinates);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return CGeo_Query_ReverseQuery
     */
    public static function fromCoordinates($latitude, $longitude) {
        return new self(new CGeo_Model_Coordinates($latitude, $longitude));
    }

    /**
     * @param Coordinates $coordinates
     *
     * @return CGeo_Query_ReverseQuery
     */
    public function withCoordinates(CGeo_Model_Coordinates $coordinates) {
        $new = clone $this;
        $new->coordinates = $coordinates;
        return $new;
    }

    /**
     * @param int $limit
     *
     * @return CGeo_Query_ReverseQuery
     */
    public function withLimit($limit) {
        $new = clone $this;
        $new->limit = $limit;
        return $new;
    }

    /**
     * @param string $locale
     *
     * @return CGeo_Query_ReverseQuery
     */
    public function withLocale($locale) {
        $new = clone $this;
        $new->locale = $locale;
        return $new;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return CGeo_Query_ReverseQuery
     */
    public function withData($name, $value) {
        $new = clone $this;
        $new->data[$name] = $value;
        return $new;
    }

    /**
     * @return CGeo_Model_Coordinates
     */
    public function getCoordinates() {
        return $this->coordinates;
    }

    /**
     * @return int
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getData($name, $default = null) {
        if (!array_key_exists($name, $this->data)) {
            return $default;
        }
        return $this->data[$name];
    }

    /**
     * @return array
     */
    public function getAllData() {
        return $this->data;
    }

    /**
     * String for logging. This is also a unique key for the query
     *
     * @return string
     */
    public function __toString() {
        return sprintf('ReverseQuery: %s', json_encode([
            'lat' => $this->getCoordinates()->getLatitude(),
            'lng' => $this->getCoordinates()->getLongitude(),
            'locale' => $this->getLocale(),
            'limit' => $this->getLimit(),
            'data' => $this->getAllData(),
        ]));
    }

}
