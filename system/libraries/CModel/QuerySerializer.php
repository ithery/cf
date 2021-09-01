<?php

class CModel_QuerySerializer {
    /**
     * Serialize from Eloquent Query Builder.
     *
     * @param \CModel_Query|\CModel_Relation $builder
     */
    public static function serialize($builder) {
        if ($builder instanceof CModel_Relation) {
            $builder = $builder->getQuery();
        }

        /** @var \CModel $model */
        $model = $builder->getModel();

        return [
            'model' => [
                'class' => \get_class($model),
                'connection' => $model->getConnectionName(),
                'eager' => c::collect($builder->getEagerLoads())->map(function ($callback) {
                    return \serialize(new CQueue_SerializableClosure($callback));
                })->all(),
                'removedScopes' => $builder->removedScopes(),
            ],
            'builder' => CModel_QuerySerializer_Query::serialize($builder->getQuery()),
        ];
    }

    /**
     * Unserialize to Eloquent Query Builder.
     */
    public static function unserialize(array $payload) {
        $model = c::tap(new $payload['model']['class'](), static function ($model) use ($payload) {
            $model->setConnection($payload['model']['connection']);
        });

        // Register model global scopes to eloquent query builder, and
        // use $payload['model']['removedScopes'] to exclude
        // global removed scopes.

        return $model->registerGlobalScopes(
            (new CModel_Query(
                CModel_QuerySerializer_Query::unserialize($payload['builder'])
            ))->setModel($model)
        )->setEagerLoads(
            c::collect($payload['model']['eager'])->map(function ($callback) {
                return \unserialize($callback)->getClosure();
            })->all()
        )->withoutGlobalScopes($payload['model']['removedScopes']);
    }
}
