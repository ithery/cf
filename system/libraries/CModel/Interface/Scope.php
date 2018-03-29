<?php

interface CModel_Interface_Scope {

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  CModel_Query  $builder
     * @param  CModel  $model
     * @return void
     */
    public function apply(CModel_Query $builder, CModel $model);
}
