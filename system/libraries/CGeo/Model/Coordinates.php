<?php

final class CGeo_Model_Coordinates {
    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude) {
        CGeo_Assert::notNull($latitude);
        CGeo_Assert::notNull($longitude);

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        CGeo_Assert::latitude($latitude);
        CGeo_Assert::longitude($longitude);

        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Returns the latitude.
     *
     * @return float
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * Returns the longitude.
     *
     * @return float
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * Returns the coordinates as a tuple.
     *
     * @return array
     */
    public function toArray() {
        return [$this->getLongitude(), $this->getLatitude()];
    }
}
