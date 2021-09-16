<?php

class CModel_MongoDB_SoftDelete_Scope extends CModel_SoftDelete_Scope {
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param CModel_Query $builder
     * @param CModel       $model
     *
     * @return void
     */
    public function apply(CModel_Query $builder, CModel $model) {
        $builder->where($model->getQualifiedStatusColumn(), '<>', '0');
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param CModel_Query $builder
     *
     * @return void
     */
    public function extend(CModel_Query $builder) {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
        $builder->onDelete(function (CModel_Query $builder) {
            $column = $this->getStatusColumn($builder);

            return $builder->update([
                $column => '0',
            ]);
        });
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param CModel_Query $builder
     *
     * @return void
     */
    protected function addRestore(CModel_Query $builder) {
        $builder->macro('restore', function (CModel_Query $builder) {
            $builder->withTrashed();

            return $builder->update([$builder->getModel()->getStatusColumn() => '1']);
        });
    }

    /**
     * Add the without-trashed extension to the builder.
     *
     * @param CModel_Query $builder
     *
     * @return void
     */
    protected function addWithoutTrashed(CModel_Query $builder) {
        $builder->macro('withoutTrashed', function (CModel_Query $builder) {
            $model = $builder->getModel();
            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedStatusColumn(),
                '<>',
                '0'
            );

            return $builder;
        });
    }

    /**
     * Add the only-trashed extension to the builder.
     *
     * @param CModel_Query $builder
     *
     * @return void
     */
    protected function addOnlyTrashed(CModel_Query $builder) {
        $builder->macro('onlyTrashed', function (CModel_Query $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->where(
                $model->getQualifiedStatusColumn(),
                '=',
                '0'
            );

            return $builder;
        });
    }
}
