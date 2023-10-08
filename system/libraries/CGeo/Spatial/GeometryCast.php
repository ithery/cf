<?php



class CGeo_Spatial_GeometryCast implements CModel_Contract_CastsAttributesInterface {
    /**
     * @var string
     */
    private string $className;

    /**
     * @param string $className
     */
    public function __construct(string $className) {
        $this->className = $className;
    }

    /**
     * @param Model                                  $model
     * @param string                                 $key
     * @param null|string|CDatabase_Query_Expression $value
     * @param array<string, mixed>                   $attributes
     *
     * @return null|Geometry
     */
    public function get($model, $key, $value, array $attributes) {
        if (!$value) {
            return null;
        }

        if ($value instanceof CDatabase_Query_Expression) {
            $wkt = $this->extractWktFromExpression($value, $model->getConnection());
            $srid = $this->extractSridFromExpression($value, $model->getConnection());

            return $this->className::fromWkt($wkt, $srid);
        }

        return $this->className::fromWkb($value);
    }

    /**
     * @param CModel                                $model
     * @param string                                $key
     * @param null|CGeo_Spatial_Type_Geometry|mixed $value
     * @param array<string, mixed>                  $attributes
     *
     * @throws InvalidArgumentException
     *
     * @return null|CDatabase_Query_Expression
     */
    public function set($model, $key, $value, array $attributes) {
        if (!$value) {
            return null;
        }

        if (is_array($value)) {
            $value = CGeo_Spatial_Type_Geometry::fromArray($value);
        }

        if ($value instanceof CDatabase_Query_Expression) {
            return $value;
        }

        if (!(is_a($value, $this->className))) {
            $geometryType = is_object($value) ? get_class($value) : gettype($value);

            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, $geometryType)
            );
        }

        return $value->toSqlExpression($model->getConnection());
    }

    private function extractWktFromExpression(CDatabase_Query_Expression $expression, CDatabase_Connection $connection): string {
        $grammar = $connection->getQueryGrammar();
        $expressionValue = $expression->getValue($grammar);

        preg_match('/ST_GeomFromText\(\'(.+)\', .+(, .+)?\)/', (string) $expressionValue, $match);

        return $match[1];
    }

    private function extractSridFromExpression(CDatabase_Query_Expression $expression, CDatabase_Connection $connection): int {
        $grammar = $connection->getQueryGrammar();
        $expressionValue = $expression->getValue($grammar);

        preg_match('/ST_GeomFromText\(\'.+\', (.+)(, .+)?\)/', (string) $expressionValue, $match);

        return (int) $match[1];
    }
}
