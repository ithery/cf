<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CMage_Mage_Trait_FillsFieldsTrait {

    /**
     * Fill a new model instance using the given request.
     *
     * @param  CMage_Request  $request
     * @param  CModel  $model
     * @return array
     */
    public static function fill(CMage_Request $request, $model) {
        return static::fillFields(
                        $request, $model, (new static($model))->creationFields($request)
        );
    }

    /**
     * Fill a new model instance using the given request.
     *
     * @param  CMage_Request  $request
     * @param  CModel  $model
     * @return array
     */
    public static function fillForUpdate(CMage_Request $request, $model) {
        return static::fillFields(
                        $request, $model, (new static($model))->updateFields($request)
        );
    }

    /**
     * Fill a new pivot model instance using the given request.
     *
     * @param  CMage_Request  $request
     * @param  CModel  $model
     * @param  CModel_Relation_Pivot  $pivot
     * @return array
     */
    public static function fillPivot(CMage_Request $request, $model, $pivot) {
        $instance = new static($model);

        return static::fillFields(
                        $request, $pivot, $instance->creationPivotFields($request, $request->relatedResource)
        );
    }

    /**
     * Fill the given fields for the model.
     *
     * @param  CMage_Request  $request
     * @param  CModel  $model
     * @param  CCollection  $fields
     * @return array
     */
    protected static function fillFields(CMage_Request $request, $model, $fields) {
        return [$model, $fields->map->fill($request, $model)->filter(function ($callback) {
                        return is_callable($callback);
                    })->values()->all()];
    }

}
