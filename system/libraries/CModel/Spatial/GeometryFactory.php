<?php
use GeoIO\Dimension;

class CModel_Spatial_GeometryFactory implements \GeoIO\Factory {
    public function createPoint($dimension, array $coordinates, $srid = null) {
        return new CModel_Spatial_Geometry_Point($coordinates['y'], $coordinates['x'], $srid);
    }

    public function createLineString($dimension, array $points, $srid = null) {
        return new CModel_Spatial_Geometry_LineString($points, $srid);
    }

    public function createLinearRing($dimension, array $points, $srid = null) {
        return new CModel_Spatial_Geometry_LineString($points, $srid);
    }

    public function createPolygon($dimension, array $lineStrings, $srid = null) {
        return new CModel_Spatial_Geometry_Polygon($lineStrings, $srid);
    }

    public function createMultiPoint($dimension, array $points, $srid = null) {
        return new CModel_Spatial_Geometry_MultiPoint($points, $srid);
    }

    public function createMultiLineString($dimension, array $lineStrings, $srid = null) {
        return new CModel_Spatial_Geometry_MultiLineString($lineStrings, $srid);
    }

    public function createMultiPolygon($dimension, array $polygons, $srid = null) {
        return new CModel_Spatial_Geometry_MultiPolygon($polygons, $srid);
    }

    public function createGeometryCollection($dimension, array $geometries, $srid = null) {
        return new CModel_Spatial_Geometry_GeometryCollection($geometries, $srid);
    }
}
