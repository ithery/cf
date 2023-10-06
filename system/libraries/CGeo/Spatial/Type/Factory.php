<?php

class CGeo_Spatial_Type_Factory implements \GeoIO\Factory {
    public function createPoint($dimension, array $coordinates, $srid = null) {
        return new CGeo_Spatial_Type_Point($coordinates['y'], $coordinates['x'], $srid);
    }

    public function createLineString($dimension, array $points, $srid = null) {
        return new CGeo_Spatial_Type_LineString($points, $srid);
    }

    public function createLinearRing($dimension, array $points, $srid = null) {
        return new CGeo_Spatial_Type_LineString($points, $srid);
    }

    public function createPolygon($dimension, array $lineStrings, $srid = null) {
        return new CGeo_Spatial_Type_Polygon($lineStrings, $srid);
    }

    public function createMultiPoint($dimension, array $points, $srid = null) {
        return new CGeo_Spatial_Type_MultiPoint($points, $srid);
    }

    public function createMultiLineString($dimension, array $lineStrings, $srid = null) {
        return new CGeo_Spatial_Type_MultiLineString($lineStrings, $srid);
    }

    public function createMultiPolygon($dimension, array $polygons, $srid = null) {
        return new CGeo_Spatial_Type_MultiPolygon($polygons, $srid);
    }

    public function createGeometryCollection($dimension, array $geometries, $srid = null) {
        return new CGeo_Spatial_Type_GeometryCollection($geometries, $srid);
    }
}
