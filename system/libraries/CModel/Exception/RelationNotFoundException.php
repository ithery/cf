<?php

class CModel_Exception_RelationNotFoundException extends CModel_Exception {
    /**
     * The name of the affected Eloquent model.
     *
     * @var string
     */
    public $model;

    /**
     * The name of the relation.
     *
     * @var string
     */
    public $relation;

    /**
     * Create a new exception instance.
     *
     * @param mixed       $model
     * @param string      $relation
     * @param null|string $type
     *
     * @return static
     */
    public static function make($model, $relation, $type = null) {
        $class = get_class($model);
        $instance = new static(
            is_null($type)
                ? "Call to undefined relationship [{$relation}] on model [{$class}]."
                : "Call to undefined relationship [{$relation}] on model [{$class}] of type [{$type}].",
        );
        $instance->model = $model;
        $instance->relation = $relation;

        return $instance;
    }
}
