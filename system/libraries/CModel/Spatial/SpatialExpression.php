<?php
class CModel_Spatial_SpatialExpression extends CDatabase_Query_Expression {
    public function getValue() {
        return "ST_GeomFromText(?, ?, 'axis-order=long-lat')";
    }

    public function getSpatialValue() {
        return $this->value->toWkt();
    }

    public function getSrid() {
        return $this->value->getSrid();
    }
}
