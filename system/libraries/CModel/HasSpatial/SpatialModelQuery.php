<?php

/**
 * @template TModel of \CModel
 *
 * @extends CModel_Query<TModel>
 *
 * @mixin \CDatabase_Query_Builder
 */
class CModel_HasSpatial_SpatialModelQuery extends CModel_Query {
    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     * @param string                                                                         $alias
     *
     * @return self
     */
    public function withDistance($column, $geometryOrColumn, string $alias = 'distance') {
        if (!$this->getQuery()->columns) {
            $this->select('*');
        }

        $this->selectRaw(
            sprintf(
                'ST_DISTANCE(%s, %s) AS %s',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $alias,
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     * @param string                                                                         $operator
     * @param int|float                                                                      $value
     *
     * @return self
     */
    public function whereDistance($column, $geometryOrColumn, string $operator, $value) {
        $this->whereRaw(
            sprintf(
                'ST_DISTANCE(%s, %s) %s ?',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $operator,
            ),
            [$value],
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     * @param string                                                                         $direction
     *
     * @return self
     */
    public function orderByDistance($column, $geometryOrColumn, string $direction = 'asc') {
        $this->orderByRaw(
            sprintf(
                'ST_DISTANCE(%s, %s) %s',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $direction,
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     * @param string                                                                         $alias
     *
     * @return self
     */
    public function withDistanceSphere(
        $column,
        $geometryOrColumn,
        string $alias = 'distance'
    ) {
        if (!$this->getQuery()->columns) {
            $this->select('*');
        }

        $this->selectRaw(
            sprintf(
                'ST_DISTANCE_SPHERE(%s, %s) AS %s',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $alias,
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     * @param string                                                                         $operator
     * @param int|float                                                                      $value
     *
     * @return self
     */
    public function whereDistanceSphere(
        $column,
        $geometryOrColumn,
        string $operator,
        $value
    ): self {
        $this->whereRaw(
            sprintf(
                'ST_DISTANCE_SPHERE(%s, %s) %s ?',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $operator,
            ),
            [$value],
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     * @param string                                                                         $direction
     *
     * @return self
     */
    public function orderByDistanceSphere(
        $column,
        $geometryOrColumn,
        string $direction = 'asc'
    ) {
        $this->orderByRaw(
            sprintf(
                'ST_DISTANCE_SPHERE(%s, %s) %s',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
                $direction
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereWithin(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_WITHIN(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereNotWithin(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_WITHIN(%s, %s) = 0',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereContains(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_CONTAINS(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereNotContains(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_CONTAINS(%s, %s) = 0',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereTouches($column, $geometryOrColumn) {
        $this->whereRaw(
            sprintf(
                'ST_TOUCHES(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereIntersects(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_INTERSECTS(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereCrosses(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_CROSSES(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereDisjoint(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_DISJOINT(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereOverlaps(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_OVERLAPS(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumn
     *
     * @return self
     */
    public function whereEquals(
        $column,
        $geometryOrColumn
    ) {
        $this->whereRaw(
            sprintf(
                'ST_EQUALS(%s, %s)',
                $this->toExpressionString($column),
                $this->toExpressionString($geometryOrColumn),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param string                                                                         $operator
     * @param int|float                                                                      $value
     *
     * @return self
     */
    public function whereSrid(
        $column,
        string $operator,
        $value
    ) {
        $this->whereRaw(
            sprintf(
                'ST_SRID(%s) %s ?',
                $this->toExpressionString($column),
                $operator,
            ),
            [$value],
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $column
     * @param string                                                                         $alias
     *
     * @return self
     */
    public function withCentroid(
        $column,
        string $alias = 'centroid'
    ) {
        $this->selectRaw(
            sprintf(
                'ST_CENTROID(%s) AS %s',
                $this->toExpressionString($column),
                $this->getGrammar()->wrap($alias),
            )
        );

        return $this;
    }

    /**
     * @param CDatabase_Contract_Query_ExpressionInterface|CGeo_Spatial_Type_Geometry|string $geometryOrColumnOrExpression
     *
     * @return string
     */
    protected function toExpressionString($geometryOrColumnOrExpression): string {
        $grammar = $this->getGrammar();

        if ($geometryOrColumnOrExpression instanceof CDatabase_Contract_Query_ExpressionInterface) {
            $expression = $geometryOrColumnOrExpression;
        } elseif ($geometryOrColumnOrExpression instanceof Geometry) {
            $expression = $geometryOrColumnOrExpression->toSqlExpression($this->getConnection());
        } else {
            $expression = c::db()->raw($grammar->wrap($geometryOrColumnOrExpression));
        }

        return (string) $expression->getValue($grammar);
    }
}
