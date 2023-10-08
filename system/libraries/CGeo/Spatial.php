<?php
/**
 * @see https://github.com/grimzy/laravel-mysql-spatial/
 */
class CGeo_Spatial {
    public static function polygon($geojson) {
        return CGeo_Spatial_Type_Polygon::fromJson($geojson);
    }

    public static function multiPolygon($geojson) {
        return CGeo_Spatial_Type_MultiPolygon::fromJson($geojson);
    }
}
