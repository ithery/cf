<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CModel_Exception_RelationNotFoundException extends RuntimeException {

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
     * @param  mixed  $model
     * @param  string  $relation
     * @return static
     */
    public static function make($model, $relation) {
        $class = get_class($model);
        $instance = new static("Call to undefined relationship [{$relation}] on model [{$class}].");
        $instance->model = $model;
        $instance->relation = $relation;
        return $instance;
    }

}
