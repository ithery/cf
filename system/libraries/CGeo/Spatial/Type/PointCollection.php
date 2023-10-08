<?php

/**
 * @property CCollection<int, CGeo_Spatial_Type_Point> $geometries
 *
 * @method CCollection<int,        CGeo_Spatial_Type_Point> getGeometries()
 * @method CGeo_Spatial_Type_Point offsetGet(int $offset)
 * @method void                    offsetSet(int $offset, Point $value)
 */
abstract class CGeo_Spatial_Type_PointCollection extends CGeo_Spatial_Type_GeometryCollection {
    protected string $collectionOf = CGeo_Spatial_Type_Point::class;

    /**
     * @param Collection<int, Point>|array<int, Point> $geometries
     * @param int                                      $srid
     *
     * @throws InvalidArgumentException
     */
    public function __construct($geometries, int $srid = 0) {
        // @phpstan-ignore-next-line
        parent::__construct($geometries, $srid);
    }
}
