<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPoint as GeoJsonMultiPoint;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

class CGeo_Spatial_Type_MultiPoint extends CGeo_Spatial_Type_PointCollection {
    /**
     * The minimum number of items required to create this collection.
     *
     * @var int
     */
    protected $minimumCollectionItems = 1;

    public function toWKT() {
        return sprintf('MULTIPOINT(%s)', (string) $this);
    }

    public static function fromWkt($wkt, $srid = 0) {
        $wktArgument = CGeo_Spatial_Type_Geometry::getWKTArgument($wkt);

        return static::fromString($wktArgument, $srid);
    }

    public static function fromString($wktArgument, $srid = 0) {
        $matches = [];
        preg_match_all('/\(\s*(\d+\s+\d+)\s*\)/', trim($wktArgument), $matches);

        $points = array_map(function ($pair) {
            return CGeo_Spatial_Type_Point::fromPair($pair);
        }, $matches[1]);

        return new static($points, $srid);
    }

    public function __toString() {
        return implode(',', array_map(function (CGeo_Spatial_Type_Point $point) {
            return sprintf('(%s)', $point->toPair());
        }, $this->items));
    }

    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonMultiPoint::class)) {
            throw new CGeo_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonMultiPoint::class . ', got ' . get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $coordinate) {
            $set[] = new CGeo_Spatial_Type_Point($coordinate[1], $coordinate[0]);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson MultiPoint that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\MultiPoint
     */
    public function jsonSerialize() {
        $points = [];
        foreach ($this->items as $point) {
            $points[] = $point->jsonSerialize();
        }

        return new GeoJsonMultiPoint($points);
    }
}
