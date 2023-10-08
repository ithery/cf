<?php

/**
 * @property CCollection<int, CGeo_Spatial_Type_LineString> $geometries
 *
 * @method CCollection<int,             CGeo_Spatial_Type_LineString> getGeometries()
 * @method CGeo_Spatial_Type_LineString offsetGet(int $offset)
 * @method void                         offsetSet(int $offset, CGeo_Spatial_Type_LineString $value)
 */
class CGeo_Spatial_Type_MultiLineString extends CGeo_Spatial_Type_GeometryCollection {
    protected string $collectionOf = CGeo_Spatial_Type_LineString::class;

    protected int $minimumGeometries = 1;

    /**
     * @param CCollection<int, LineString>|array<int, LineString> $geometries
     * @param int                                                 $srid
     *
     * @throws InvalidArgumentException
     */
    public function __construct($geometries, int $srid = 0) {
        // @phpstan-ignore-next-line
        parent::__construct($geometries, $srid);
    }

    public function toWkt(): string {
        $wktData = $this->getWktData();

        return "MULTILINESTRING({$wktData})";
    }

    public function getWktData(): string {
        return $this->geometries
            ->map(static function (CGeo_Spatial_Type_LineString $lineString): string {
                $wktData = $lineString->getWktData();

                return "({$wktData})";
            })->join(', ');
    }
}
