<?php

class CGeo_Spatial_Type_LineString extends CGeo_Spatial_Type_PointCollection {
    protected int $minimumGeometries = 2;

    public function toWkt(): string {
        $wktData = $this->getWktData();

        return "LINESTRING({$wktData})";
    }

    public function getWktData(): string {
        return $this->geometries
            ->map(static function (CGeo_Spatial_Type_Point $point): string {
                return $point->getWktData();
            })
            ->join(', ');
    }
}
