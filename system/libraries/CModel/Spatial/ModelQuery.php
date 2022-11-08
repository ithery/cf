<?php
class CModel_Spatial_ModelQuery extends CModel_Query {
    public function update(array $values) {
        foreach ($values as $key => &$value) {
            if ($value instanceof CModel_Spatial_Contract_GeometryInterface) {
                $value = $this->asWKT($value);
            }
        }

        return parent::update($values);
    }

    protected function asWKT(CModel_Spatial_Contract_GeometryInterface $geometry) {
        return new CModel_Spatial_SpatialExpression($geometry);
    }
}
