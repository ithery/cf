<?php
class CModel_Spatial_SpatialExpression extends CDatabase_Query_Expression {
    public function getValue(CDatabase_Grammar $grammar) {
        // return "ST_GeomFromText(?, ?, 'axis-order=long-lat')";
        return 'ST_GeomFromText(?, ?)';
    }

    public function getSpatialValue() {
        return $this->value->toWkt();
    }

    public function getSrid() {
        return $this->value->getSrid();
    }
}
