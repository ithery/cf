<?php

/**
 * @template TCollection of \CModel_Collection
 *
 * @property static string $collectionClass
 */
trait CModel_Trait_HasCollection {
    /**
     * The Eloquent collection class to use for the model.
     *
     * @var class-string<\CModel_Collection<*, *>>
     */
    protected static $collectionClass = CCollection::class;

    /**
     * Create a new Model Collection instance.
     *
     * @param array<array-key, \CModel> $models
     *
     * @return TCollection
     */
    public function newCollection(array $models = []) {
        return new static::$collectionClass($models);
    }
}
