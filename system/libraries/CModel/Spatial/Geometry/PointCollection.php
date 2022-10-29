<?php

abstract class CModel_Spatial_Geometry_PointCollection extends CModel_Spatial_Geometry_GeometryCollection {
    /**
     * The class of the items in the collection.
     *
     * @var string
     */
    protected $collectionItemType = CModel_Spatial_Geometry_Point::class;

    public function toPairList() {
        return implode(',', array_map(function (CModel_Spatial_Geometry_Point $point) {
            return $point->toPair();
        }, $this->items));
    }

    public function offsetSet($offset, $value) {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    /**
     * @return array|\Grimzy\LaravelMysqlSpatial\Types\Point[]
     */
    public function getPoints() {
        return $this->items;
    }

    /**
     * @param \CModel_Spatial_Geometry_Point $point
     *
     * @deprecated 2.1.0 Use array_unshift($multipoint, $point); instead
     * @see array_unshift
     * @see ArrayAccess
     */
    public function prependPoint(CModel_Spatial_Geometry_Point $point) {
        array_unshift($this->items, $point);
    }

    /**
     * @param \CModel_Spatial_Geometry_Point $point
     *
     * @deprecated 2.1.0 Use $multipoint[] = $point; instead
     * @see ArrayAccess
     */
    public function appendPoint(CModel_Spatial_Geometry_Point $point) {
        $this->items[] = $point;
    }

    /**
     * @param $index
     * @param \CModel_Spatial_Geometry_Point $point
     *
     * @deprecated 2.1.0 Use array_splice($multipoint, $index, 0, [$point]); instead
     * @see array_splice
     * @see ArrayAccess
     */
    public function insertPoint($index, CModel_Spatial_Geometry_Point $point) {
        if (count($this->items) - 1 < $index) {
            throw new InvalidArgumentException('$index is greater than the size of the array');
        }

        array_splice($this->items, $index, 0, [$point]);
    }
}
