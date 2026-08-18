<?php

/**
 * @property CCollection<int, Polygon> $geometries
 *
 * @method CCollection<int,          Polygon> getGeometries()
 * @method CGeo_Spatial_Type_Polygon offsetGet(int $offset)
 * @method void                      offsetSet(int $offset, CGeo_Spatial_Type_Polygon $value)
 */
class CGeo_Spatial_Type_MultiPolygon extends CGeo_Spatial_Type_GeometryCollection {
    protected string $collectionOf = CGeo_Spatial_Type_Polygon::class;

    protected int $minimumGeometries = 1;

    /**
     * @param Collection<int, Polygon>|array<int, Polygon> $geometries
     * @param int                                          $srid
     *
     * @throws InvalidArgumentException
     */
    public function __construct($geometries, int $srid = 0) {
        // @phpstan-ignore-next-line
        parent::__construct($geometries, $srid);
    }

    public function toWkt(): string {
        $wktData = $this->getWktData();

        return "MULTIPOLYGON({$wktData})";
    }

    public function getWktData(): string {
        return $this->geometries->map(static function (CGeo_Spatial_Type_Polygon $polygon): string {
            $wktData = $polygon->getWktData();

            return "({$wktData})";
        })->join(', ');
    }
}
