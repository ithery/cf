<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CMage_Mage_Trait_ValidationTrait {

    /**
     * Validate a resource creation request.
     *
     * @param  CMage_Request  $request
     * @return void
     */
    public function validateForAdd(CMage_Request $request) {
        $this->validatorForAdd($request)->validate();
    }

    /**
     * Create a validator instance for a resource creation request.
     *
     * @param  CMage_Request  $request
     * @return CValidation_Validator
     */
    public function validatorForAdd(CMage_Request $request) {
        return CValidation_Factory::make($request->all(), $this->rulesForAdd($request))
                        ->after(function ($validator) use ($request) {
                            $this->afterValidation($request, $validator);
                            $this->afterAddValidation($request, $validator);
                        });
    }

    /**
     * Get the validation rules for a resource creation request.
     *
     * @param  CMage_Request  $request
     * @return array
     */
    public function rulesForAdd(CMage_Request $request) {
        return $this->formatRules($request, (new static($this->newModel()))
                                ->creationFields($request)
                                ->mapWithKeys(function ($field) use ($request) {
                                    return $field->getCreationRules($request);
                                })->all());
    }

    /**
     * Get the creation validation rules for a specific field.
     *
     * @param  CMage_Request  $request
     * @param  string  $field
     * @return array
     */
    public function creationRulesFor(CMage_Request $request, $field) {
        return $this->formatRules($request, (new static($this->newModel()))
                                ->availableFields($request)
                                ->where('attribute', $field)
                                ->mapWithKeys(function ($field) use ($request) {
                                    return $field->getCreationRules($request);
                                })->all());
    }

    /**
     * Validate a resource update request.
     *
     * @param  CMage_Request  $request
     * @return void
     */
    public function validateForEdit(CMage_Request $request) {
        $this->validatorForEdit($request)->validate();
    }

    /**
     * Create a validator instance for a resource update request.
     *
     * @param  CMage_Request  $request
     * @return CValidation_Validator
     */
    public function validatorForEdit(CMage_Request $request) {
        return CValidation_Factory::make($request->all(), $this->rulesForUpdate($request))
                        ->after(function ($validator) use ($request) {
                            $this->afterValidation($request, $validator);
                            $this->afterEditValidation($request, $validator);
                        });
    }

    /**
     * Get the validation rules for a resource update request.
     *
     * @param  CMage_Request  $request
     * @return array
     */
    public function rulesForUpdate(CMage_Request $request) {
        return $this->formatRules($request, (new static($this->newModel()))
                                ->updateFields($request)
                                ->mapWithKeys(function ($field) use ($request) {
                                    return $field->getUpdateRules($request);
                                })->all());
    }

    /**
     * Get the update validation rules for a specific field.
     *
     * @param  CMage_Request  $request
     * @param  string  $field
     * @return array
     */
    public function updateRulesFor(CMage_Request $request, $field) {
        return $this->formatRules($request, (new static($this->newModel()))
                                ->availableFields($request)
                                ->where('attribute', $field)
                                ->mapWithKeys(function ($field) use ($request) {
                                    return $field->getUpdateRules($request);
                                })->all());
    }

    /**
     * Validate a resource attachment request.
     *
     * @param  CMage_Request  $request
     * @return void
     */
    public function validateForAttachment(CMage_Request $request) {
        $this->validatorForAttachment($request)->validate();
    }

    /**
     * Create a validator instance for a resource attachment request.
     *
     * @param  CMage_Request  $request
     * @return CValidation_Validator
     */
    public function validatorForAttachment(CMage_Request $request) {
        return CValidation_Factory::make($request->all(), $this->rulesForAttachment($request));
    }

    /**
     * Get the validation rules for a resource attachment request.
     *
     * @param  CMage_Request  $request
     * @return array
     */
    public function rulesForAttachment(CMage_Request $request) {
        return $this->formatRules($request, (new static($this->newModel()))
                                ->creationPivotFields($request, $request->relatedResource)
                                ->mapWithKeys(function ($field) use ($request) {
                                    return $field->getCreationRules($request);
                                })->all());
    }

    /**
     * Validate a resource attachment update request.
     *
     * @param  CMage_Request  $request
     * @return void
     */
    public function validateForAttachmentUpdate(CMage_Request $request) {
        $this->validatorForAttachmentUpdate($request)->validate();
    }

    /**
     * Create a validator instance for a resource attachment update request.
     *
     * @param  CMage_Request  $request
     * @return CValidation_Validator
     */
    public function validatorForAttachmentUpdate(CMage_Request $request) {
        return CValidation_Factory::make($request->all(), $this->rulesForAttachmentUpdate($request));
    }

    /**
     * Get the validation rules for a resource attachment update request.
     *
     * @param  CMage_Request  $request
     * @return array
     */
    public function rulesForAttachmentUpdate(CMage_Request $request) {
        return $this->formatRules($request, (new static($this->newModel()))
                                ->updatePivotFields($request, $request->relatedResource)
                                ->mapWithKeys(function ($field) use ($request) {
                                    return $field->getUpdateRules($request);
                                })->all());
    }

    /**
     * Perform any final formatting of the given validation rules.
     *
     * @param  CMage_Request  $request
     * @param  array  $rules
     * @return array
     */
    protected static function formatRules(CMage_Request $request, array $rules) {
        $replacements = array_filter([
            '{{resourceId}}' => $request->resourceId,
        ]);

        if (empty($replacements)) {
            return $rules;
        }

        return CF::collect($rules)->map(function ($rules) use ($replacements) {
                    return CF::collect($rules)->map(function ($rule) use ($replacements) {
                                return is_string($rule) ? str_replace(array_keys($replacements), array_values($replacements), $rule) : $rule;
                            })->all();
                })->all();
    }

    /**
     * Get the validation attribute for a specific field.
     *
     * @param  CMage_Request  $request
     * @param  string  $field
     * @return string
     */
    public function validationAttributeFor(CMage_Request $request, $field) {
        return (new static($this->newModel()))
                        ->availableFields($request)
                        ->firstWhere('resourceName', $field)
                        ->getValidationAttribute($request);
    }

    /**
     * Handle any post-validation processing.
     *
     * @param  CMage_Request  $request
     * @param  CValidation_Validator  $validator
     * @return void
     */
    protected static function afterValidation(CMage_Request $request, $validator) {
        //
    }

    /**
     * Handle any post-creation validation processing.
     *
     * @param  CMage_Request  $request
     * @param  CValidation_Validator  $validator
     * @return void
     */
    protected static function afterAddValidation(CMage_Request $request, $validator) {
        //
    }

    /**
     * Handle any post-update validation processing.
     *
     * @param  CMage_Request  $request
     * @param  CValidation_Validator  $validator
     * @return void
     */
    protected static function afterEditValidation(CMage_Request $request, $validator) {
        //
    }

}
