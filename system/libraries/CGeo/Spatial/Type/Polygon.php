<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\Polygon as GeoJsonPolygon;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

class CGeo_Spatial_Type_Polygon extends CGeo_Spatial_Type_MultiLineString {
    public function toWKT() {
        return sprintf('POLYGON(%s)', (string) $this);
    }

    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonPolygon::class)) {
            throw new CGeo_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonPolygon::class . ', got ' . get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $coordinates) {
            $points = [];
            foreach ($coordinates as $coordinate) {
                $points[] = new CGeo_Spatial_Type_Point($coordinate[1], $coordinate[0]);
            }
            $set[] = new CGeo_Spatial_Type_LineString($points);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson Polygon that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\Polygon
     */
    public function jsonSerialize() {
        $linearRings = [];
        foreach ($this->items as $lineString) {
            $linearRings[] = new \GeoJson\Geometry\LinearRing($lineString->jsonSerialize()->getCoordinates());
        }

        return new GeoJsonPolygon($linearRings);
    }
}
