<?php

class CModel_Factory_BelongsToManyRelationship {
    /**
     * The related factory instance.
     *
     * @var \CModel_Factory_Factory|\CCollection|\CModel|array
     */
    protected $factory;

    /**
     * The pivot attributes / attribute resolver.
     *
     * @var callable|array
     */
    protected $pivot;

    /**
     * The relationship name.
     *
     * @var string
     */
    protected $relationship;

    /**
     * Create a new attached relationship definition.
     *
     * @param \CModel_Factory_Factory|\CCollection|\CModel|array $factory
     * @param callable|array                                     $pivot
     * @param string                                             $relationship
     *
     * @return void
     */
    public function __construct($factory, $pivot, $relationship) {
        $this->factory = $factory;
        $this->pivot = $pivot;
        $this->relationship = $relationship;
    }

    /**
     * Create the attached relationship for the given model.
     *
     * @param \CModel $model
     *
     * @return void
     */
    public function createFor(CModel $model) {
        CCollection::wrap($this->factory instanceof CModel_Factory_Factory ? $this->factory->create([], $model) : $this->factory)->each(function ($attachable) use ($model) {
            $model->{$this->relationship}()->attach(
                $attachable,
                is_callable($this->pivot) ? call_user_func($this->pivot, $model) : $this->pivot
            );
        });
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
