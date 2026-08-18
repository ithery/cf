<?php

use Point as geoPHPPoint;
use Polygon as geoPHPPolygon;
use Geometry as geoPHPGeometry;
use LineString as geoPHPLineString;
use MultiPoint as geoPHPMultiPoint;
use MultiPolygon as geoPHPMultiPolygon;
use MultiLineString as geoPHPMultiLineString;
use GeometryCollection as geoPHPGeometryCollection;

class CGeo_Spatial_Factory {
    /**
     * @param string|\GeoJson\GeoJson $value
     *
     * @return CGeo_Spatial_Type_Geometry
     */
    public static function parse($value) {
        if ($value instanceof GeoJson\GeoJson) {
            $value = json_encode($value);
        }

        try {
            /** @var geoPHPGeometry|false $geoPHPGeometry */
            $geoPHPGeometry = geoPHP::load($value);
        } finally {
            if (!isset($geoPHPGeometry) || !$geoPHPGeometry) {
                throw new InvalidArgumentException('Invalid spatial value');
            }
        }

        return self::createFromGeometry($geoPHPGeometry);
    }

    /**
     * @param geoPHPGeometry $geometry
     *
     * @return CGeo_Spatial_Type_Geometry
     */
    protected static function createFromGeometry(geoPHPGeometry $geometry) {
        $srid = is_int($geometry->getSRID()) ? $geometry->getSRID() : 0;

        if ($geometry instanceof geoPHPPoint) {
            if ($geometry->coords[0] === null || $geometry->coords[1] === null) {
                throw new InvalidArgumentException('Invalid spatial value');
            }

            return new CGeo_Spatial_Type_Point($geometry->coords[1], $geometry->coords[0], $srid);
        }

        /** @var geoPHPGeometryCollection $geometry */
        $components = c::collect($geometry->components)
            ->map(static function (geoPHPGeometry $geometryComponent) {
                return self::createFromGeometry($geometryComponent);
            });

        if (get_class($geometry) === geoPHPMultiPoint::class) {
            return new CGeo_Spatial_Type_MultiPoint($components, $srid);
        }

        if (get_class($geometry) === geoPHPLineString::class) {
            return new CGeo_Spatial_Type_LineString($components, $srid);
        }

        if (get_class($geometry) === geoPHPPolygon::class) {
            return new CGeo_Spatial_Type_Polygon($components, $srid);
        }

        if (get_class($geometry) === geoPHPMultiLineString::class) {
            return new CGeo_Spatial_Type_MultiLineString($components, $srid);
        }

        if (get_class($geometry) === geoPHPMultiPolygon::class) {
            return new CGeo_Spatial_Type_MultiPolygon($components, $srid);
        }

        return new CGeo_Spatial_Type_GeometryCollection($components, $srid);
    }
}
