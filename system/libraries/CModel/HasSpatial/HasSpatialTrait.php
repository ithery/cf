<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @method static static|CModel_HasSpatial_SpatialModelQuery withDistance($column, $geometryOrColumn, string $alias = 'distance')
 * @method static static|CModel_HasSpatial_SpatialModelQuery whereDistance($column, $geometryOrColumn, string $operator, $value)
 * @method static static|CModel_HasSpatial_SpatialModelQuery orderByDistance($column, $geometryOrColumn, string $direction = 'asc')
 * @method static static|CModel_HasSpatial_SpatialModelQuery withDistanceSphere($column, $geometryOrColumn, string $alias = 'distance')
 * @method static static|CModel_HasSpatial_SpatialModelQuery whereDistanceSphere($column, $geometryOrColumn, string $operator, $value)
 * @method static static|CModel_HasSpatial_SpatialModelQuery orderByDistanceSphere($column, $geometryOrColumn, string $direction = 'asc')
 * @method static static|CModel_HasSpatial_SpatialModelQuery whereWithin($column, $geometryOrColumn)
 * @method static static|CModel_HasSpatial_SpatialModelQuery whereNotWithin($column, $geometryOrColumn)
 * @method static static|CModel_HasSpatial_SpatialModelQuery whereContains($column, $geometryOrColumn)
 * @method static static|CModel_HasSpatial_SpatialModelQuery whereNotContains($column, $geometryOrColumn)
 * @method static static|CModel_HasSpatial_SpatialModelQuery whereTouches($column, $geometryOrColumn)
 */
trait CModel_HasSpatial_HasSpatialTrait {
    public function newModelBuilder($query) {
        return new CModel_HasSpatial_SpatialModelQuery($query);
    }
}
