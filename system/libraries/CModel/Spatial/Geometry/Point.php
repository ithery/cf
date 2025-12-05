<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\Point as GeoJsonPoint;

class CModel_Spatial_Geometry_Point extends CModel_Spatial_GeometryAbstract {
    protected $lat;

    protected $lng;

    /**
     * Construct a new Point object
     *
     * @param float $lat latitude value
     * @param float $lng longitude value
     * @param int $srid srid value (optional, defaults to 0)
     */
    public function __construct($lat, $lng, $srid = 0) {
        parent::__construct($srid);

        $this->lat = (float) $lat;
        $this->lng = (float) $lng;
    }

    /**
     * Returns the latitude value of the point
     *
     * @return float The latitude value
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * Set the latitude value of the point
     *
     * @param float $lat The new latitude value
     */
    public function setLat($lat) {
        $this->lat = (float) $lat;
    }


    /**
     * Returns the longitude value of the point
     *
     * @return float The longitude value
     */
    public function getLng() {
        return $this->lng;
    }


    /**
     * Set the longitude value of the point
     *
     * @param float $lng The new longitude value
     */
    public function setLng($lng) {
        $this->lng = (float) $lng;
    }

    /**
     * Returns a string representation of the point as a pair of longitude and latitude
     * values separated by a space.
     *
     * @return string The string representation of the point
     */
    public function toPair() {
        return $this->getLng() . ' ' . $this->getLat();
    }

    /**
     * Create a new Point object from a pair of longitude and latitude values
     * separated by a space.
     *
     * @param string $pair The pair of longitude and latitude values
     * @param int    $srid The SRID value (default is 0)
     * @return static A new Point object
     */
    public static function fromPair($pair, $srid = 0) {
        list($lng, $lat) = explode(' ', trim($pair, "\t\n\r \x0B()"));

        return new static((float) $lat, (float) $lng, (int) $srid);
    }

    /**
     * Returns a Well-Known Text (WKT) representation of the point.
     *
     * @return string The WKT representation of the point
     */
    public function toWKT() {
        return sprintf('POINT(%s)', (string) $this);
    }

    /**
     * Create a new Point object from a Well-Known Text (WKT) representation of the point
     *
     * @param string $wktArgument The WKT representation of the point
     * @param int    $srid The SRID value (default is 0)
     * @return static A new Point object
     */
    public static function fromString($wktArgument, $srid = 0) {
        return static::fromPair($wktArgument, $srid);
    }

    /**
     * Returns a string representation of the point as a pair of longitude and latitude
     * values separated by a space.
     *
     * @return string The string representation of the point
     */
    public function __toString() {
        return $this->getLng() . ' ' . $this->getLat();
    }

    /**
     * @param $geoJson  \GeoJson\Feature\Feature|string
     *
     * @return \CModel_Spatial_Geometry_Point
     */
    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonPoint::class)) {
            throw new CModel_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonPoint::class . ', got ' . get_class($geoJson));
        }

        $coordinates = $geoJson->getCoordinates();

        return new self($coordinates[1], $coordinates[0]);
    }

    /**
     * Convert to GeoJson Point that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\Point
     */
    public function jsonSerialize() {
        return new GeoJsonPoint([$this->getLng(), $this->getLat()]);
    }
}
