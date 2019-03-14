<?php


trait CModel_LogActivity_Trait_DetectsChanges
{
    protected $oldAttributes = [];

    protected static function bootDetectsChanges()
    {
        if (static::eventsToBeRecorded()->contains('updated')) {
            static::updating(function (CModel $model) {

                //temporary hold the original attributes on the model
                //as we'll need these in the updating event
                $oldValues = $model->replicate()->setRawAttributes($model->getOriginal());

                $model->oldAttributes = static::logChanges($oldValues);
            });
        }
    }

    /**
     * [attributesToBeLogged description]
     *
     * @method attributesToBeLogged
     *
     * @return array               [description]
     */
    public function attributesToBeLogged()
    {
        $attributes = [];

        if (isset(static::$logFillable) && static::$logFillable) {
            $attributes = array_merge($attributes, $this->getFillable());
        }

        if ($this->shouldLogUnguarded()) {
            $attributes = array_merge($attributes, array_diff(array_keys($this->getAttributes()), $this->getGuarded()));
        }

        if (isset(static::$logAttributes) && is_array(static::$logAttributes)) {
            $attributes = array_merge($attributes, array_diff(static::$logAttributes, ['*']));

            if (in_array('*', static::$logAttributes)) {
                $attributes = array_merge($attributes, array_keys($this->getAttributes()));
            }
        }

        if (isset(static::$logAttributesToIgnore) && is_array(static::$logAttributesToIgnore)) {
            $attributes = array_diff($attributes, static::$logAttributesToIgnore);
        }

        return $attributes;
    }

    /**
     * [shouldLogOnlyDirty description]
     *
     * @method shouldLogOnlyDirty
     *
     * @return bool             [description]
     */
    public function shouldLogOnlyDirty()
    {
        if (! isset(static::$logOnlyDirty)) {
            return false;
        }

        return static::$logOnlyDirty;
    }

    /**
     * [shouldLogUnguarded description]
     *
     * @method shouldLogUnguarded
     *
     * @return bool             [description]
     */
    public function shouldLogUnguarded()
    {
        if (! isset(static::$logUnguarded)) {
            return false;
        }

        if (! static::$logUnguarded) {
            return false;
        }

        if (in_array('*', $this->getGuarded())) {
            return false;
        }

        return true;
    }

    /**
     * [attributeValuesToBeLogged description]
     *
     * @method attributeValuesToBeLogged
     *
     * @param  string                    $processingEvent [description]
     *
     * @return array                                     [description]
     */
    public function attributeValuesToBeLogged(string $processingEvent)
    {
        if (! count($this->attributesToBeLogged())) {
            return [];
        }

        $properties['attributes'] = static::logChanges(
            $this->exists
                ? $this->fresh() ?: $this
                : $this
        );

        if (static::eventsToBeRecorded()->contains('updated') && $processingEvent == 'updated') {
            $nullProperties = array_fill_keys(array_keys($properties['attributes']), null);

            $properties['old'] = array_merge($nullProperties, $this->oldAttributes);
        }

        if ($this->shouldLogOnlyDirty() && isset($properties['old'])) {
            $properties['attributes'] = array_udiff_assoc(
                $properties['attributes'],
                $properties['old'],
                function ($new, $old) {
                    if ($new > $old) {
                        return 1;
                    } elseif ($new < $old) {
                        return -1;
                    } else {
                        return 0;
                    }
                }
            );
            $properties['old'] = collect($properties['old'])
                ->only(array_keys($properties['attributes']))
                ->all();
        }

        return $properties;
    }

    /**
     * [logChanges description]
     *
     * @method logChanges
     *
     * @param  CModel      $model [description]
     *
     * @return array            [description]
     */
    public static function logChanges(CModel $model)
    {
        $changes = [];
        $attributes = $model->attributesToBeLogged();
        $model = clone $model;
        $model->append(array_filter($attributes, function ($key) use ($model) {
            return $model->hasGetMutator($key);
        }));
        $model->setHidden(array_diff($model->getHidden(), $attributes));
        $collection = collect($model);

        foreach ($attributes as $attribute) {
            if (cstr::contains($attribute, '.')) {
                $changes += self::getRelatedModelAttributeValue($model, $attribute);
            } else {
                $changes += $collection->only($attribute)->toArray();
            }
        }

        return $changes;
    }

    /**
     * [getRelatedModelAttributeValue description]
     *
     * @method getRelatedModelAttributeValue
     *
     * @param  CModel                        $model     [description]
     * @param  string                        $attribute [description]
     *
     * @return array                                   [description]
     */
    protected static function getRelatedModelAttributeValue(CModel $model, string $attribute)
    {
        if (substr_count($attribute, '.') > 1) {
            throw CModel_LogActivity_Exception_CouldNotLogChangesException::invalidAttribute($attribute);
        }

        list($relatedModelName, $relatedAttribute) = explode('.', $attribute);

        $relatedModel = $model->$relatedModelName ?: $model->$relatedModelName();

        return ["{$relatedModelName}.{$relatedAttribute}" => $relatedModel->$relatedAttribute ?: null];
    }
}
