<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon as GeoJsonMultiPolygon;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

class CGeo_Spatial_Type_MultiPolygon extends CGeo_Spatial_Type_GeometryCollection {
    /**
     * The minimum number of items required to create this collection.
     *
     * @var int
     */
    protected $minimumCollectionItems = 1;

    /**
     * The class of the items in the collection.
     *
     * @var string
     */
    protected $collectionItemType = CGeo_Spatial_Type_Polygon::class;

    public function toWKT() {
        return sprintf('MULTIPOLYGON(%s)', (string) $this);
    }

    public function __toString() {
        return implode(',', array_map(function (CGeo_Spatial_Type_Polygon $polygon) {
            return sprintf('(%s)', (string) $polygon);
        }, $this->items));
    }

    public static function fromString($wktArgument, $srid = 0) {
        $parts = preg_split('/(\)\s*\)\s*,\s*\(\s*\()/', $wktArgument, -1, PREG_SPLIT_DELIM_CAPTURE);
        $polygons = static::assembleParts($parts);

        return new static(array_map(function ($polygonString) {
            return CGeo_Spatial_Type_Polygon::fromString($polygonString);
        }, $polygons), $srid);
    }

    /**
     * Get the polygons that make up this MultiPolygon.
     *
     * @return array|Polygon[]
     */
    public function getPolygons() {
        return $this->items;
    }

    /**
     * Make an array like this:
     * "((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1",
     * ")), ((",
     * "-1 -1,-1 -2,-2 -2,-2 -1,-1 -1",
     * ")), ((",
     * "-1 -1,-1 -2,-2 -2,-2 -1,-1 -1))".
     *
     * Into:
     * "((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1))",
     * "((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1))",
     * "((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1))"
     *
     * @param array $parts
     *
     * @return array
     */
    protected static function assembleParts(array $parts) {
        $polygons = [];
        $count = count($parts);

        for ($i = 0; $i < $count; $i++) {
            if ($i % 2 !== 0) {
                list($end, $start) = explode(',', $parts[$i]);
                $polygons[$i - 1] .= $end;
                $polygons[++$i] = $start . $parts[$i];
            } else {
                $polygons[] = $parts[$i];
            }
        }

        return $polygons;
    }

    public function offsetSet($offset, $value) {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonMultiPolygon::class)) {
            throw new CGeo_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonMultiPolygon::class . ', got ' . get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $polygonCoordinates) {
            $lineStrings = [];
            foreach ($polygonCoordinates as $lineStringCoordinates) {
                $points = [];
                foreach ($lineStringCoordinates as $lineStringCoordinate) {
                    $points[] = new CGeo_Spatial_Type_Point($lineStringCoordinate[1], $lineStringCoordinate[0]);
                }
                $lineStrings[] = new CGeo_Spatial_Type_LineString($points);
            }
            $set[] = new CGeo_Spatial_Type_Polygon($lineStrings);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson MultiPolygon that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\MultiPolygon
     */
    public function jsonSerialize() {
        $polygons = [];
        foreach ($this->items as $polygon) {
            $polygons[] = $polygon->jsonSerialize();
        }

        return new GeoJsonMultiPolygon($polygons);
    }
}
