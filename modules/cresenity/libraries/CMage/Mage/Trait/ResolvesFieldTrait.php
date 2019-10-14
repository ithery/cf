<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CMage_Mage_Trait_ResolvesFieldTrait {

    /**
     * Resolve the index fields.
     *
     * @param  CMage_Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function indexFields(CMage_Request $request) {
        return $this->resolveFields($request)->reject(function ($field) use ($request) {
                    return $field instanceof ListableField ||
                            !$field->showOnIndex ||
                            !$field->authorize($request);
                })->each(function ($field) use ($request) {
                    if ($field instanceof Resolvable && !$field->pivot) {
                        $field->resolveForDisplay($this->resource);
                    }

                    if ($field instanceof Resolvable && $field->pivot) {
                        $accessor = $this->pivotAccessorFor($request, $request->viaResource);

                        $field->resolveForDisplay(isset($this->{$accessor}) && $this->{$accessor} != null ? $this->{$accessor} : new CModel_Relation_Pivot);
                    }
                });
    }

    /**
     * Resolve the detail fields.
     *
     * @param  CMage_Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function detailFields(CMage_Request $request) {
        return $this->resolveFields($request)->reject(function ($field) use ($request) {
                    return !$field->showOnDetail || !$field->authorize($request);
                })->when(in_array(Actionable::class, class_uses_recursive(static::newModel())), function ($fields) {
                    return $fields->push(MorphMany::make(__('Actions'), 'actions', ActionResource::class));
                })->each(function ($field) use ($request) {
                    if ($field instanceof Resolvable && !$field->pivot) {
                        $field->resolveForDisplay($this->resource);
                    }
                    if ($field instanceof Resolvable && $field->pivot) {
                        $accessor = $this->pivotAccessorFor($request, $request->viaResource);

                        $field->resolveForDisplay(isset($this->{$accessor}) && $this->{$accessor} != null ? $this->{$accessor} : new Pivot);
                    }
                });
    }

    /**
     * Resolve the creation fields.
     *
     * @param  CMage_Request  $request
     * @return CCollection
     */
    public function creationFields(CMage_Request $request) {
        return $this->removeNonCreationFields($this->resolveFields($request));
    }

    /**
     * Resolve the creation pivot fields for a related resource.
     *
     * @param  CMage_Request  $request
     * @param  \Illuminate\Support\Collection  $relatedResource
     * @return Collection
     */
    public function creationPivotFields(CMage_Request $request, $relatedResource) {
        return $this->removeNonCreationFields(
                        $this->resolvePivotFields($request, $relatedResource)
        );
    }

    /**
     * Remove non-creation fields from the given collection.
     *
     * @param  \Illuminate\Support\Collection  $fields
     * @return \Illuminate\Support\Collection
     */
    protected function removeNonCreationFields(Collection $fields) {
        return $fields->reject(function ($field) {
                    return $field instanceof ListableField ||
                            $field instanceof ResourceToolElement ||
                            ($field instanceof ID && $field->attribute === $this->resource->getKeyName()) ||
                            $field->attribute === 'ComputedField' ||
                            !$field->showOnCreation;
                });
    }

    /**
     * Resolve the update fields.
     *
     * @param  CMage_Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function updateFields(CMage_Request $request) {
        return $this->removeNonUpdateFields($this->resolveFields($request));
    }

    /**
     * Resolve the update pivot fields for a related resource.
     *
     * @param  CMage_Request  $request
     * @param  \Illuminate\Support\Collection  $relatedResource
     * @return Collection
     */
    public function updatePivotFields(CMage_Request $request, $relatedResource) {
        return $this->removeNonUpdateFields(
                        $this->resolvePivotFields($request, $relatedResource)
        );
    }

    /**
     * Remove non-update fields from the given collection.
     *
     * @param  \Illuminate\Support\Collection  $fields
     * @return \Illuminate\Support\Collection
     */
    protected function removeNonUpdateFields(Collection $fields) {
        return $fields->reject(function ($field) {
                    return $field instanceof ListableField ||
                            $field instanceof ResourceToolElement ||
                            ($field instanceof ID && $field->attribute === $this->resource->getKeyName()) ||
                            $field->attribute === 'ComputedField' ||
                            !$field->showOnUpdate;
                });
    }

    /**
     * Resolve the given fields to their values.
     *
     * @param  CMage_Request  $request
     * @return \Illuminate\Support\Collection
     */
    protected function resolveFields(CMage_Request $request) {
        $fields = CF::tap($this->availableFields($request), function ($fields) {
                    $fields->whereInstanceOf(Resolvable::class)->each->resolve($this->resource);
                });

        $fields = $fields->filter->authorize($request)->values();

        return $request->viaRelationship() ? $this->withPivotFields($request, $fields->all()) : $fields;
    }

    /**
     * Resolve the field for the given attribute.
     *
     * @param  CMage_Request  $request
     * @param  string  $attribute
     * @return \Laravel\Nova\Fields\Field
     */
    public function resolveFieldForAttribute(CMage_Request $request, $attribute) {
        return $this->resolveFields($request)->findFieldByAttribute($attribute);
    }

    /**
     * Resolve the inverse field for the given relationship attribute.
     *
     * This is primarily used for Relatable rule to check if has-one / morph-one relationships are "full".
     *
     * @param  CMage_Request  $request
     * @param  string  $attribute
     * @param  string  $morphType
     * @return \Illuminate\Support\Collection
     */
    public function resolveInverseFieldsForAttribute(CMage_Request $request, $attribute, $morphType = null) {
        $field = $this->resolveFieldForAttribute($request, $attribute);

        if (!isset($field->resourceClass)) {
            return collect();
        }

        $relatedResource = $field instanceof CModel_Relation_MorphTo ? CMage::mageForKey($morphType ? $morphType: $request->{$attribute . '_type'}) : (isset($field->resourceClass) ?$field->resourceClass: null);

        $relatedResource = new $relatedResource($relatedResource::newModel());

        $result = $relatedResource->availableFields($request)->reject(function ($f) use ($field) {
                    return isset($f->attribute) &&
                            isset($field->inverse) &&
                            $f->attribute !== $field->inverse;
                })->filter(function ($field) use ($request) {
            return isset($field->resourceClass) &&
                    $field->resourceClass == $request->resource();
        });

        return $result;
    }

    /**
     * Resolve the resource's avatar URL, if applicable.
     *
     * @param  CMage_Request  $request
     * @return string|null
     */
    public function resolveAvatarUrl(CMage_Request $request) {
        $fields = $this->resolveFields($request);

        $field = $fields->first(function ($field) {
            return $field instanceof Avatar;
        });

        if ($field) {
            return $field->resolveThumbnailUrl();
        }
    }

    /**
     * Get the panels that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceDetailRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availablePanels(ResourceDetailRequest $request) {
        $panels = collect(array_values($this->fields($request)))
                        ->whereInstanceOf(Panel::class)->values();

        $default = Panel::defaultNameFor($request->newResource());

        return $panels->when($panels->where('name', $default)->isEmpty(), function ($panels) use ($default) {
                    return $panels->push((new Panel($default))->withToolbar());
                })->all();
    }

    /**
     * Get the fields that are available for the given request.
     *
     * @param  CMage_Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableFields(CMage_Request $request) {
        return new CMage_Mage_FieldCollection(array_values($this->filter($this->fields($request))));
    }

    /**
     * Merge the available pivot fields with the given fields.
     *
     * @param  CMage_Request  $request
     * @param  array  $fields
     * @return \Illuminate\Support\Collection
     */
    protected function withPivotFields(CMage_Request $request, array $fields) {
        $pivotFields = $this->resolvePivotFields($request, $request->viaResource)->all();

        if ($index = $this->indexToInsertPivotFields($request, $fields)) {
            array_splice($fields, $index + 1, 0, $pivotFields);
        } else {
            $fields = array_merge($fields, $pivotFields);
        }

        return new FieldCollection($fields);
    }

    /**
     * Resolve the pivot fields for the requested resource.
     *
     * @param  CMage_Request  $request
     * @param  string  $relatedResource
     * @return \Illuminate\Support\Collection
     */
    public function resolvePivotFields(CMage_Request $request, $relatedResource) {
        $fields = $this->pivotFieldsFor($request, $relatedResource);

        return (new CMage_Mage_FieldCollection($this->filter($fields->each(function ($field) use ($request, $relatedResource) {
                            if ($field instanceof Resolvable) {
                                $accessor = $this->pivotAccessorFor($request, $relatedResource);

                                $field->resolve(isset($this->{$accessor}) ?$this->{$accessor}: new CModel_Relation_Pivot);
                            }
                        })->filter->authorize($request)->values()->all())))->values();
    }

    /**
     * Get the pivot fields for the resource and relation.
     *
     * @param  CMage_Request  $request
     * @param  string  $relatedResource
     * @return \Illuminate\Support\Collection
     */
    protected function pivotFieldsFor(CMage_Request $request, $relatedResource) {
        $field = $this->availableFields($request)->first(function ($field) use ($relatedResource) {
            return isset($field->resourceName) &&
                    $field->resourceName == $relatedResource;
        });

        if ($field && isset($field->fieldsCallback)) {
            return CF::collect(array_values(
                                    $this->filter(call_user_func($field->fieldsCallback, $request, $this->resource))
                    ))->each(function ($field) {
                        $field->pivot = true;
                    });
        }

        return CF::collect([]);
    }

    /**
     * Get the name of the pivot accessor for the requested relationship.
     *
     * @param  CMage_Request  $request
     * @param  string  $relatedResource
     * @return string
     */
    public function pivotAccessorFor(CMage_Request $request, $relatedResource) {
        $field = $this->availableFields($request)->first(function ($field) use ($request, $relatedResource) {
            return ($field instanceof BelongsToMany ||
                    $field instanceof MorphToMany) &&
                    $field->resourceName == $relatedResource;
        });

        return $this->resource->{$field->manyToManyRelationship}()->getPivotAccessor();
    }

    /**
     * Get the index where the pivot fields should be spliced into the field array.
     *
     * @param  CMage_Request  $request
     * @param  array  $fields
     * @return int
     */
    protected function indexToInsertPivotFields(CMage_Request $request, array $fields) {
        foreach ($fields as $index => $field) {
            if (isset($field->resourceName) &&
                    $field->resourceName == $request->viaResource) {
                return $index;
            }
        }
    }

    /**
     * Get the displayable pivot model name from a field.
     *
     * @param  CMage_Request  $request
     * @param  string  $field
     * @return string|null
     */
    public function pivotNameForField(CMage_Request $request, $field) {
        $field = $this->availableFields($request)->where('attribute', $field)->first();

        if (!$field || (!$field instanceof BelongsToMany &&
                !$field instanceof MorphToMany)) {
            return self::DEFAULT_PIVOT_NAME;
        }

        if (isset($field->pivotName)) {
            return $field->pivotName;
        }
    }

}
