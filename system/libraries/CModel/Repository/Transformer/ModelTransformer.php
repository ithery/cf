<?php

use League\Fractal\TransformerAbstract;

/**
 * Class ModelTransformer.
 */
class CModel_Repository_Transformer_ModelTransformer extends TransformerAbstract {
    public function transform(CModel_Repository_Contract_Transformable $model) {
        return $model->transform();
    }
}
