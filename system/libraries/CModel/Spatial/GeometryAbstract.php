<?php

use GeoIO\Factory;
use GeoJson\GeoJson;
use GeoIO\WKB\Parser\Parser;

abstract class CModel_Spatial_GeometryAbstract implements CModel_Spatial_Contract_GeometryInterface, CInterface_Jsonable, \JsonSerializable {
    protected static $wkb_types = [
        1 => CModel_Spatial_Geometry_Point::class,
        2 => CModel_Spatial_Geometry_LineString::class,
        3 => CModel_Spatial_Geometry_Polygon::class,
        4 => CModel_Spatial_Geometry_MultiPoint::class,
        5 => CModel_Spatial_Geometry_MultiLineString::class,
        6 => CModel_Spatial_Geometry_MultiPolygon::class,
        7 => CModel_Spatial_Geometry_GeometryCollection::class,
    ];

    protected $srid;

    public function __construct($srid = 0) {
        $this->srid = (int) $srid;
    }

    public function getSrid() {
        return $this->srid;
    }

    public function setSrid($srid) {
        $this->srid = (int) $srid;
    }

    public static function getWKTArgument($value) {
        $left = strpos($value, '(');
        $right = strrpos($value, ')');

        return substr($value, $left + 1, $right - $left - 1);
    }

    public static function getWKTClass($value) {
        $left = strpos($value, '(');
        $type = trim(substr($value, 0, $left));

        switch (strtoupper($type)) {
            case 'POINT':
                return CModel_Spatial_Geometry_Point::class;
            case 'LINESTRING':
                return CModel_Spatial_Geometry_LineString::class;
            case 'POLYGON':
                return CModel_Spatial_Geometry_Polygon::class;
            case 'MULTIPOINT':
                return CModel_Spatial_Geometry_MultiPoint::class;
            case 'MULTILINESTRING':
                return CModel_Spatial_Geometry_MultiLineString::class;
            case 'MULTIPOLYGON':
                return CModel_Spatial_Geometry_MultiPolygon::class;
            case 'GEOMETRYCOLLECTION':
                return CModel_Spatial_Geometry_GeometryCollection::class;
            default:
                throw new CModel_Spatial_Exception_UnknownWKTTypeException('Type was ' . $type);
        }
    }

    public static function fromWKB($wkb) {
        $srid = substr($wkb, 0, 4);
        $srid = unpack('L', $srid)[1];

        $wkb = substr($wkb, 4);
        $parser = new Parser(new Factory());

        /** @var Geometry $parsed */
        $parsed = $parser->parse($wkb);

        if ($srid > 0) {
            $parsed->setSrid($srid);
        }

        return $parsed;
    }

    public static function fromWKT($wkt, $srid = null) {
        $wktArgument = static::getWKTArgument($wkt);

        return static::fromString($wktArgument, $srid);
    }

    public static function fromJson($geoJson) {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if ($geoJson->getType() === 'FeatureCollection') {
            return CModel_Spatial_Geometry_GeometryCollection::fromJson($geoJson);
        }

        if ($geoJson->getType() === 'Feature') {
            $geoJson = $geoJson->getGeometry();
        }

        $type = 'CModel_Spatial_Geometry_' . $geoJson->getType();

        return $type::fromJson($geoJson);
    }

    public function toJson($options = 0) {
        return json_encode($this, $options);
    }
}
