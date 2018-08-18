<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:23:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
/**
 * Coordinate Implementation
 *
 * @author    Marcus Jaschen <mjaschen@gmail.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/mjaschen/phpgeo
 */

namespace Location;

use Location\Distance\DistanceInterface;
use Location\Formatter\Coordinate\FormatterInterface;

/**
 * Coordinate Implementation
 *
 */
class CGeo_Location_Coordinate implements CGeo_Location_GeometryInterface {

    /**
     * @var float
     */
    protected $lat;

    /**
     * @var float
     */
    protected $lng;

    /**
     * @var Ellipsoid
     */
    protected $ellipsoid;

    /**
     * @param float $lat -90.0 .. +90.0
     * @param float $lng -180.0 .. +180.0
     * @param Ellipsoid $ellipsoid if omitted, WGS-84 is used
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($lat, $lng, $ellipsoid = null) {
        if (!$this->isValidLatitude($lat)) {
            throw new \InvalidArgumentException("Latitude value must be numeric -90.0 .. +90.0 (given: {$lat})");
        }
        if (!$this->isValidLongitude($lng)) {
            throw new \InvalidArgumentException("Longitude value must be numeric -180.0 .. +180.0 (given: {$lng})");
        }
        $this->lat = (float) $lat;
        $this->lng = (float) $lng;
        if ($ellipsoid !== null) {
            $this->ellipsoid = $ellipsoid;
            return;
        }
        $this->ellipsoid = CGeo_Location_Ellipsoid::createDefault();
    }

    /**
     * @return float
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLng() {
        return $this->lng;
    }

    /**
     * Returns an array containing the point
     *
     * @return array
     */
    public function getPoints() {
        return [$this];
    }

    /**
     * @return CGeo_Location_Ellipsoid
     */
    public function getEllipsoid() {
        return $this->ellipsoid;
    }

    /**
     * Calculates the distance between the given coordinate
     * and this coordinate.
     *
     * @param CGeo_Location_Coordinate $coordinate
     * @param DistanceInterface $calculator instance of distance calculation class
     *
     * @return float
     */
    public function getDistance(CGeo_Location_Coordinate $coordinate, CGeo_Location_Distance_DistanceInterface $calculator = null) {
        if ($calculator == null) {
            $calculator = new CGeo_Location_Distance_Haversine();
        }
        return $calculator->getDistance($this, $coordinate);
    }

    /**
     * @param FormatterInterface $formatter
     *
     * @return mixed
     */
    public function format(FormatterInterface $formatter) {
        return $formatter->format($this);
    }

    /**
     * Validates latitude
     *
     * @param float $latitude
     *
     * @return bool
     */
    protected function isValidLatitude($latitude) {
        return $this->isNumericInBounds($latitude, -90.0, 90.0);
    }

    /**
     * Validates longitude
     *
     * @param float $longitude
     *
     * @return bool
     */
    protected function isValidLongitude($longitude) {
        return $this->isNumericInBounds($longitude, -180.0, 180.0);
    }

    /**
     * Checks if the given value is (1) numeric, and (2) between lower
     * and upper bounds (including the bounds values).
     *
     * @param float $value
     * @param float $lower
     * @param float $upper
     *
     * @return bool
     */
    protected function isNumericInBounds($value, $lower, $upper) {
        if ($value < $lower || $value > $upper) {
            return false;
        }
        return true;
    }

}
