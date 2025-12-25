<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon as GeoJsonMultiPolygon;

class CModel_Spatial_Geometry_MultiPolygon extends CModel_Spatial_Geometry_GeometryCollection {
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
    protected $collectionItemType = CModel_Spatial_Geometry_Polygon::class;

    /**
     * Returns a Well-Known Text (WKT) representation of the MultiPolygon.
     *
     * @return string The WKT representation of the MultiPolygon
     */
    public function toWKT() {
        return sprintf('MULTIPOLYGON(%s)', (string) $this);
    }

    /**
     * Returns a string representation of the MultiPolygon.
     *
     * The MultiPolygon is represented as a comma-separated list of polygons.
     * Each polygon is represented as a string enclosed in parentheses, with its
     * points separated by commas.
     *
     * @return string The string representation of the MultiPolygon
     */
    public function __toString() {
        return implode(',', array_map(function (CModel_Spatial_Geometry_Polygon $polygon) {
            return sprintf('(%s)', (string) $polygon);
        }, $this->items));
    }

    public static function fromString($wktArgument, $srid = 0) {
        $parts = preg_split('/(\)\s*\)\s*,\s*\(\s*\()/', $wktArgument, -1, PREG_SPLIT_DELIM_CAPTURE);
        $polygons = static::assembleParts($parts);

        return new static(array_map(function ($polygonString) {
            return CModel_Spatial_Geometry_Polygon::fromString($polygonString);
        }, $polygons), $srid);
    }

    /**
     * Get the polygons that make up this MultiPolygon.
     *
     * @return array|CModel_Spatial_Geometry_Polygon[]
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

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value) {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    /**
     * Creates a new MultiPolygon from a GeoJson MultiPolygon object.
     *
     * @param mixed $geoJson The GeoJson object to create a MultiPolygon from
     *
     * @throws CModel_Spatial_Exception_InvalidGeoJsonException If the passed GeoJson object is not of the correct type
     *
     * @return CModel_Spatial_Geometry_MultiPolygon A new MultiPolygon object
     */
    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonMultiPolygon::class)) {
            throw new CModel_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonMultiPolygon::class . ', got ' . get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $polygonCoordinates) {
            $lineStrings = [];
            foreach ($polygonCoordinates as $lineStringCoordinates) {
                $points = [];
                foreach ($lineStringCoordinates as $lineStringCoordinate) {
                    $points[] = new CModel_Spatial_Geometry_Point($lineStringCoordinate[1], $lineStringCoordinate[0]);
                }
                $lineStrings[] = new CModel_Spatial_Geometry_LineString($points);
            }
            $set[] = new CModel_Spatial_Geometry_Polygon($lineStrings);
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

    public function getCentroid() {
        // Ambil GeoJSON.
        $geoJson = json_encode($this->jsonSerialize());

        // Load ke geoPHP Geometry.
        $geometry = \geoPHP::load($geoJson, 'json');

        if (!$geometry) {
            throw new \Exception('Failed to parse polygon GeoJSON via geoPHP');
        }

        // Hitung centroid menggunakan geoPHP.
        $centroid = $geometry->centroid();

        if (!$centroid) {
            throw new \Exception('Failed to calculate centroid via geoPHP');
        }

        // geoPHP Point: ->y() = lat, ->x() = lng
        return new CModel_Spatial_Geometry_Point($centroid->y(), $centroid->x());
    }
}
