<?php

class CModel_Scout_SearchableScope implements CModel_Interface_Scope {
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \CModel_Query $builder
     * @param \CModel       $model
     *
     * @return void
     */
    public function apply(CModel_Query $builder, CModel $model) {
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param \CModel_Query $builder
     *
     * @return void
     */
    public function extend(CModel_Query $builder) {
        $builder->macro('searchable', function (CModel_Query $builder, $chunk = null) {
            $scoutKeyName = $builder->getModel()->getScoutKeyName();
            $builder->chunkById($chunk ?: CF::config('model.scout.chunk.searchable', 500), function ($models) {
                $models->filter->shouldBeSearchable()->searchable();

                CEvent::dispatch(new CModel_Scout_Event_ModelsImported($models));
            }, $builder->qualifyColumn($scoutKeyName), $scoutKeyName);
        });

        $builder->macro('unsearchable', function (CModel_Query $builder, $chunk = null) {
            $scoutKeyName = $builder->getModel()->getScoutKeyName();
            $builder->chunkById($chunk ?: CF::config('model.scout.chunk.unsearchable', 500), function ($models) {
                $models->unsearchable();

                CEvent::dispatch(new CModel_Scout_Event_ModelsFlushed($models));
            }, $builder->qualifyColumn($scoutKeyName), $scoutKeyName);
        });

        CModel_Relation_HasManyThrough::macro('searchable', function ($chunk = null) {
            /** @var CModel_Query $this */
            $this->chunkById($chunk ?: CF::config('model.scout.chunk.searchable', 500), function ($models) {
                $models->filter->shouldBeSearchable()->searchable();

                CEvent::dispatch(new CModel_Scout_Event_ModelsImported($models));
            });
        });

        CModel_Relation_HasManyThrough::macro('unsearchable', function ($chunk = null) {
            /** @var CModel_Query $this */
            $this->chunkById($chunk ?: CF::config('model.scout.chunk.searchable', 500), function ($models) {
                $models->unsearchable();

                CEvent::dispatch(new CModel_Scout_Event_ModelsFlushed($models));
            });
        });
    }
}
