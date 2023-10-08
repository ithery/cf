<?php

class CGeo_Spatial_Type_Polygon extends CGeo_Spatial_Type_MultiLineString {
    public function toWkt(): string {
        $wktData = $this->getWktData();

        return "POLYGON({$wktData})";
    }

    public function getWktData(): string {
        return $this->geometries
            ->map(static function (CGeo_Spatial_Type_LineString $lineString): string {
                $wktData = $lineString->getWktData();

                return "({$wktData})";
            })->join(', ');
    }
}
