<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiLineString as GeoJsonMultiLineString;

class CModel_Spatial_Geometry_MultiLineString extends CModel_Spatial_Geometry_GeometryCollection {
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
    protected $collectionItemType = CModel_Spatial_Geometry_LineString::class;

    public function getLineStrings() {
        return $this->items;
    }

    public function toWKT() {
        return sprintf('MULTILINESTRING(%s)', (string) $this);
    }

    public static function fromString($wktArgument, $srid = 0) {
        $str = preg_split('/\)\s*,\s*\(/', substr(trim($wktArgument), 1, -1));
        $lineStrings = array_map(function ($data) {
            return CModel_Spatial_Geometry_LineString::fromString($data);
        }, $str);

        return new static($lineStrings, $srid);
    }

    public function __toString() {
        return implode(',', array_map(function (CModel_Spatial_Geometry_LineString $lineString) {
            return sprintf('(%s)', (string) $lineString);
        }, $this->getLineStrings()));
    }

    public function offsetSet($offset, $value) {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonMultiLineString::class)) {
            throw new CModel_Spatial_Exception_InvalidGeoJsonException('Expected ' . GeoJsonMultiLineString::class . ', got ' . get_class($geoJson));
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
     * Convert to GeoJson Point that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\MultiLineString
     */
    public function jsonSerialize() {
        $lineStrings = [];

        foreach ($this->items as $lineString) {
            $lineStrings[] = $lineString->jsonSerialize();
        }

        return new GeoJsonMultiLineString($lineStrings);
    }
}
