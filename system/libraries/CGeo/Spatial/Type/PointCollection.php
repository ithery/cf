<?php

use ArrayAccess;
use InvalidArgumentException;

abstract class CGeo_Spatial_Type_PointCollection extends CGeo_Spatial_Type_GeometryCollection {
    /**
     * The class of the items in the collection.
     *
     * @var string
     */
    protected $collectionItemType = CGeo_Spatial_Type_Point::class;

    public function toPairList() {
        return implode(',', array_map(function (CGeo_Spatial_Type_Point $point) {
            return $point->toPair();
        }, $this->items));
    }

    public function offsetSet($offset, $value) {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    /**
     * @return array|\CGeo_Spatial_Type_Point[]
     */
    public function getPoints() {
        return $this->items;
    }

    /**
     * @param \CGeo_Spatial_Type_Point $point
     *
     * @deprecated 2.1.0 Use array_unshift($multipoint, $point); instead
     * @see array_unshift
     * @see ArrayAccess
     */
    public function prependPoint(CGeo_Spatial_Type_Point $point) {
        array_unshift($this->items, $point);
    }

    /**
     * @param \CGeo_Spatial_Type_Point $point
     *
     * @deprecated 2.1.0 Use $multipoint[] = $point; instead
     * @see ArrayAccess
     */
    public function appendPoint(CGeo_Spatial_Type_Point $point) {
        $this->items[] = $point;
    }

    /**
     * @param $index
     * @param \CGeo_Spatial_Type_Point $point
     *
     * @deprecated 2.1.0 Use array_splice($multipoint, $index, 0, [$point]); instead
     * @see array_splice
     * @see ArrayAccess
     */
    public function insertPoint($index, CGeo_Spatial_Type_Point $point) {
        if (count($this->items) - 1 < $index) {
            throw new InvalidArgumentException('$index is greater than the size of the array');
        }

        array_splice($this->items, $index, 0, [$point]);
    }
}
