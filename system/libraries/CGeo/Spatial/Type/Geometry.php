<?php

use GeoJson\GeoJson;
use GeoIO\WKB\Parser\Parser;
use Illuminate\Contracts\Support\Jsonable;
use Grimzy\LaravelMysqlSpatial\Exceptions\UnknownWKTTypeException;

abstract class CGeo_Spatial_Type_Geometry implements CGeo_Spatial_Contract_GeometryInterface, Jsonable, \JsonSerializable {
    protected static $wkb_types = [
        1 => CGeo_Spatial_Type_Point::class,
        2 => CGeo_Spatial_Type_LineString::class,
        3 => CGeo_Spatial_Type_Polygon::class,
        4 => CGeo_Spatial_Type_MultiPoint::class,
        5 => CGeo_Spatial_Type_MultiLineString::class,
        6 => CGeo_Spatial_Type_MultiPolygon::class,
        7 => CGeo_Spatial_Type_GeometryCollection::class,
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
                return CGeo_Spatial_Type_Point::class;
            case 'LINESTRING':
                return CGeo_Spatial_Type_LineString::class;
            case 'POLYGON':
                return CGeo_Spatial_Type_Polygon::class;
            case 'MULTIPOINT':
                return CGeo_Spatial_Type_MultiPoint::class;
            case 'MULTILINESTRING':
                return CGeo_Spatial_Type_MultiLineString::class;
            case 'MULTIPOLYGON':
                return CGeo_Spatial_Type_MultiPolygon::class;
            case 'GEOMETRYCOLLECTION':
                return CGeo_Spatial_Type_GeometryCollection::class;
            default:
                throw new CGeo_Spatial_Exception_UnknownWKTTypeException('Type was ' . $type);
        }
    }

    public static function fromWKB($wkb) {
        $srid = substr($wkb, 0, 4);
        $srid = unpack('L', $srid)[1];

        $wkb = substr($wkb, 4);
        $parser = new Parser(new CGeo_Spatial_Type_Factory());

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
            return CGeo_Spatial_Type_GeometryCollection::fromJson($geoJson);
        }

        if ($geoJson->getType() === 'Feature') {
            $geoJson = $geoJson->getGeometry();
        }

        $type = '\Grimzy\LaravelMysqlSpatial\Types\\' . $geoJson->getType();

        return $type::fromJson($geoJson);
    }

    public function toJson($options = 0) {
        return json_encode($this, $options);
    }
}
