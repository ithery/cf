<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\Polygon as GeoJsonPolygon;

class CModel_Spatial_Geometry_Polygon extends CModel_Spatial_Geometry_MultiLineString {
    public function toWKT() {
        return sprintf('POLYGON(%s)', (string) $this);
    }

    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonPolygon::class)) {
            throw new CModel_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonPolygon::class . ', got ' . get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $coordinates) {
            $points = [];
            foreach ($coordinates as $coordinate) {
                $points[] = new CModel_Spatial_Geometry_Point($coordinate[1], $coordinate[0]);
            }
            $set[] = new CModel_Spatial_Geometry_LineString($points);
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

    /**
     * Returns the centroid of this polygon.
     *
     * Requires the geoPHP library, install via composer require phayes/geophp.
     *
     * @throws \RuntimeException if geoPHP is not found
     * @throws \RuntimeException if failed to parse polygon GeoJSON via geoPHP
     *
     * @return CModel_Spatial_Geometry_Point
     */
    public function getCentroid() {
        if (!class_exists('geoPHP')) {
            throw new \RuntimeException('geoPHP library not found. Install via composer require phayes/geophp');
        }

        // Ambil GeoJSON dari polygon
        $geoJson = json_encode($this->jsonSerialize());

        // Load menjadi geometry geoPHP
        $polygon = \geoPHP::load($geoJson, 'json');

        if (!$polygon) {
            throw new \RuntimeException('Failed to parse polygon GeoJSON via geoPHP');
        }

        // Dapatkan centroid
        $centroid = $polygon->getCentroid();

        return new CModel_Spatial_Geometry_Point(
            $centroid->getY(), // lat
            $centroid->getX()  // lng
        );
    }
}
