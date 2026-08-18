<?php

class CGeo_Spatial_Type_Point extends CGeo_Spatial_Type_Geometry {
    public float $latitude;

    public float $longitude;

    public function __construct(float $latitude, float $longitude, int $srid = 0) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->srid = $srid;
    }

    public function toWkt(): string {
        $wktData = $this->getWktData();

        return "POINT({$wktData})";
    }

    public function getWktData(): string {
        return "{$this->longitude} {$this->latitude}";
    }

    /**
     * @return array{0: float, 1: float}
     */
    public function getCoordinates(): array {
        return [
            $this->longitude,
            $this->latitude,
        ];
    }
}
