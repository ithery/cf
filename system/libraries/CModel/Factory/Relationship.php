<?php

class CModel_Factory_Relationship {
    /**
     * The related factory instance.
     *
     * @var \CModel_Factory_Factory
     */
    protected $factory;

    /**
     * The relationship name.
     *
     * @var string
     */
    protected $relationship;

    /**
     * Create a new child relationship instance.
     *
     * @param \CModel_Factory_Factory $factory
     * @param string                  $relationship
     *
     * @return void
     */
    public function __construct(CModel_Factory_Factory $factory, $relationship) {
        $this->factory = $factory;
        $this->relationship = $relationship;
    }

    /**
     * Create the child relationship for the given parent model.
     *
     * @param \CModel $parent
     *
     * @return void
     */
    public function createFor(CModel $parent) {
        $relationship = $parent->{$this->relationship}();

        if ($relationship instanceof CModel_Relation_MorphOneOrMany) {
            $this->factory->state([
                $relationship->getMorphType() => $relationship->getMorphClass(),
                $relationship->getForeignKeyName() => $relationship->getParentKey(),
            ])->create([], $parent);
        } elseif ($relationship instanceof CModel_Relation_HasOneOrMany) {
            $this->factory->state([
                $relationship->getForeignKeyName() => $relationship->getParentKey(),
            ])->create([], $parent);
        } elseif ($relationship instanceof CModel_Relation_BelongsToMany) {
            $relationship->attach($this->factory->create([], $parent));
        }
    }

    /**
     * Specify the model instances to always use when creating relationships.
     *
     * @param \CCollection $recycle
     *
     * @return $this
     */
    public function recycle($recycle) {
        $this->factory = $this->factory->recycle($recycle);

        return $this;
    }
}
