<?php

class CModel_Spatial_QueryBuilder extends CDatabase_Query_Builder {
    public function cleanBindings(array $bindings) {
        $spatialBindings = [];
        foreach ($bindings as &$binding) {
            if ($binding instanceof CModel_Spatial_SpatialExpression) {
                $spatialBindings[] = $binding->getSpatialValue();
                $spatialBindings[] = $binding->getSrid();
            } else {
                $spatialBindings[] = $binding;
            }
        }

        return parent::cleanBindings($spatialBindings);
    }
}
