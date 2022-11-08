<?php

interface CModel_Spatial_Contract_GeometryInterface {
    public function toWKT();

    public static function fromWKT($wkt, $srid = 0);

    public function __toString();

    public static function fromString($wktArgument, $srid = 0);

    public static function fromJson($geoJson);

    public function jsonSerialize();
}
