<?php
/**
 * @see https://github.com/grimzy/laravel-mysql-spatial/
 */
class CGeo_Spatial {
    public static function point($geojson) {
        return CGeo_Spatial_Type_Point::fromJson($geojson);
    }

    public static function multiPoint($geojson) {
        return CGeo_Spatial_Type_MultiPoint::fromJson($geojson);
    }

    public static function lineString($geojson) {
        return CGeo_Spatial_Type_LineString::fromJson($geojson);
    }

    public static function multiLineString($geojson) {
        return CGeo_Spatial_Type_MultiLineString::fromJson($geojson);
    }

    public static function polygon($geojson) {
        return CGeo_Spatial_Type_Polygon::fromJson($geojson);
    }

    public static function multiPolygon($geojson) {
        return CGeo_Spatial_Type_MultiPolygon::fromJson($geojson);
    }
}
