<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\LineString as GeoJsonLineString;

class CModel_Spatial_Geometry_LineString extends CModel_Spatial_Geometry_PointCollection {
    /**
     * The minimum number of items required to create this collection.
     *
     * @var int
     */
    protected $minimumCollectionItems = 2;

    public function toWKT() {
        return sprintf('LINESTRING(%s)', $this->toPairList());
    }

    public static function fromWkt($wkt, $srid = 0) {
        $wktArgument = CModel_Spatial_GeometryAbstract::getWKTArgument($wkt);

        return static::fromString($wktArgument, $srid);
    }

    public static function fromString($wktArgument, $srid = 0) {
        $pairs = explode(',', trim($wktArgument));
        $points = array_map(function ($pair) {
            return CModel_Spatial_Geometry_Point::fromPair($pair);
        }, $pairs);

        return new static($points, $srid);
    }

    public function __toString() {
        return $this->toPairList();
    }

    /**
     * @param mixed $geoJson
     *
     * @return CModel_Spatial_Geometry_LineString
     */
    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonLineString::class)) {
            throw new CModel_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonLineString::class . ', got ' . get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $coordinate) {
            $set[] = new CModel_Spatial_Geometry_Point($coordinate[1], $coordinate[0]);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson LineString that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\LineString
     */
    public function jsonSerialize() {
        $points = [];
        foreach ($this->items as $point) {
            $points[] = $point->jsonSerialize();
        }

        return new GeoJsonLineString($points);
    }
}
