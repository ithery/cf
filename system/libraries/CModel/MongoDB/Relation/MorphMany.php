<?php

class CModel_MongoDB_Relation_MorphMany extends CModel_Relation_MorphMany {
    /**
     * Get the name of the "where in" method for eager loading.
     *
     * @param CModel $model
     * @param string $key
     *
     * @return string
     */
    protected function whereInMethod(CModel $model, $key) {
        return 'whereIn';
    }
}
