<?php

class CModel_Factory_BelongsToRelationship {
    /**
     * The related factory instance.
     *
     * @var \CModel_Factory_Factory|\CModel
     */
    protected $factory;

    /**
     * The relationship name.
     *
     * @var string
     */
    protected $relationship;

    /**
     * The cached, resolved parent instance ID.
     *
     * @var mixed
     */
    protected $resolved;

    /**
     * Create a new "belongs to" relationship definition.
     *
     * @param \CModel_Factory_Factory|\CModel $factory
     * @param string                          $relationship
     *
     * @return void
     */
    public function __construct($factory, $relationship) {
        $this->factory = $factory;
        $this->relationship = $relationship;
    }

    /**
     * Get the parent model attributes and resolvers for the given child model.
     *
     * @param \CModel $model
     *
     * @return array
     */
    public function attributesFor(CModel $model) {
        $relationship = $model->{$this->relationship}();

        return $relationship instanceof CModel_Relation_MorphTo ? [
            $relationship->getMorphType() => $this->factory instanceof CModel_Factory_Factory ? $this->factory->newModel()->getMorphClass() : $this->factory->getMorphClass(),
            $relationship->getForeignKeyName() => $this->resolver($relationship->getOwnerKeyName()),
        ] : [
            $relationship->getForeignKeyName() => $this->resolver($relationship->getOwnerKeyName()),
        ];
    }

    /**
     * Get the deferred resolver for this relationship's parent ID.
     *
     * @param null|string $key
     *
     * @return \Closure
     */
    protected function resolver($key) {
        return function () use ($key) {
            if (!$this->resolved) {
                $instance = $this->factory instanceof CModel_Factory_Factory
                    ? ($this->factory->getRandomRecycledModel($this->factory->modelName()) ?? $this->factory->create())
                    : $this->factory;

                return $this->resolved = $key ? $instance->{$key} : $instance->getKey();
            }

            return $this->resolved;
        };
    }

    /**
     * Specify the model instances to always use when creating relationships.
     *
     * @param \CCollection $recycle
     *
     * @return $this
     */
    public function recycle($recycle) {
        if ($this->factory instanceof CModel_Factory_Factory) {
            $this->factory = $this->factory->recycle($recycle);
        }

        return $this;
    }
}
