<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
trait CComponent_Concern_ValidatesInputTrait {

    protected $errorBag;

    public function getErrorBag() {
        return $this->errorBag ? $this->errorBag : new CBase_MessageBag();
    }

    public function addError($name, $message) {
        return $this->getErrorBag()->add($name, $message);
    }

    public function setErrorBag($bag) {
        
        return $this->errorBag = $bag instanceof CBase_MessageBagInterface ? $bag : new CBase_MessageBag($bag);
    }

    public function resetErrorBag($field = null) {
        $fields = (array) $field;

        if (empty($fields)) {
            return $this->errorBag = new MessageBag;
        }

        $this->setErrorBag(
                $this->errorBagExcept($fields)
        );
    }

    public function clearValidation($field = null) {
        $this->resetErrorBag($field);
    }

    public function resetValidation($field = null) {
        $this->resetErrorBag($field);
    }

    public function errorBagExcept($field) {
        $fields = (array) $field;

        return new CApp_Message_Bag(
                c::collect($this->getErrorBag())
                        ->reject(function ($messages, $messageKey) use ($fields) {
                            return collect($fields)->some(function ($field) use ($messageKey) {
                                        return str($messageKey)->is($field);
                                    });
                        })
                        ->toArray()
        );
    }

    protected function getRules() {
        if (method_exists($this, 'rules'))
            return $this->rules();
        if (property_exists($this, 'rules'))
            return $this->rules;

        return [];
    }

    protected function getMessages() {
        if (method_exists($this, 'messages'))
            return $this->messages();
        if (property_exists($this, 'messages'))
            return $this->messages;

        return [];
    }

    protected function getValidationAttributes() {
        if (method_exists($this, 'validationAttributes'))
            return $this->validationAttributes();
        if (property_exists($this, 'validationAttributes'))
            return $this->validationAttributes;

        return [];
    }

    public function rulesForModel($name) {
        if (empty($this->getRules()))
            return c::collect();

        return c::collect($this->getRules())
                        ->filter(function ($value, $key) use ($name) {
                            return $this->beforeFirstDot($key) === $name;
                        });
    }

    public function hasRuleFor($dotNotatedProperty) {
        $propertyWithStarsInsteadOfNumbers = $this->ruleWithNumbersReplacedByStars($dotNotatedProperty);

        // If property has numeric indexes in it,
        if ($dotNotatedProperty !== $propertyWithStarsInsteadOfNumbers) {
            return collect($this->getRules())->keys()->contains($propertyWithStarsInsteadOfNumbers);
        }

        return c::collect($this->getRules())
                        ->keys()
                        ->map(function ($key) {
                            return (string) str($key)->before('.*');
                        })->contains($dotNotatedProperty);
    }

    public function ruleWithNumbersReplacedByStars($dotNotatedProperty) {
        // Convert foo.0.bar.1 -> foo.*.bar.*
        return (string) str($dotNotatedProperty)
                        // Replace all numeric indexes with an array wildcard: (.0., .10., .007.) => .*.
                        // In order to match overlapping numerical indexes (foo.1.2.3.4.name),
                        // We need to use a positive look-behind, that's technically all the magic here.
                        // For better understanding, see: https://regexr.com/5d1n3
                        ->replaceMatches('/(?<=(\.))\d+\./', '*.')
                        // Replace all numeric indexes at the end of the name with an array wildcard
                        // (Same as the previous regex, but ran only at the end of the string)
                        // For better undestanding, see: https://regexr.com/5d1n6
                        ->replaceMatches('/\.\d+$/', '.*');
    }

    public function missingRuleFor($dotNotatedProperty) {
        return !$this->hasRuleFor($dotNotatedProperty);
    }

    public function validate($rules = null, $messages = [], $attributes = []) {
        list($rules, $messages, $attributes) = $this->providedOrGlobalRulesMessagesAndAttributes($rules, $messages, $attributes);

        $data = $this->prepareForValidation(
                $this->getDataForValidation($rules)
        );

        $validator = CValidation::createValidator($data, $rules, $messages, $attributes);

        $this->shortenModelAttributes($data, $rules, $validator);

        $validatedData = $validator->validate();

        $this->resetErrorBag();

        return $validatedData;
    }

    public function validateOnly($field, $rules = null, $messages = [], $attributes = []) {
        list($rules, $messages, $attributes) = $this->providedOrGlobalRulesMessagesAndAttributes($rules, $messages, $attributes);

        // If the field is "items.0.foo", validation rules for "items.*.foo", "items.*", etc. are applied.
        $rulesForField = c::collect($rules)->filter(function ($rule, $fullFieldKey) use ($field) {
                    return c::str($field)->is($fullFieldKey);
                })->toArray();

        $ruleKeysForField = array_keys($rulesForField);

        $data = $this->prepareForValidation(
                $this->getDataForValidation($rules)
        );

        $validator = CValidation::createValidator($data, $rulesForField, $messages, $attributes);

        $this->shortenModelAttributes($data, $rulesForField, $validator);

        try {
            $result = $validator->validate();
        } catch (CValidation_Exception $e) {
            $messages = $e->validator->getMessageBag();
            $target = new CComponent_ObjectPrybar($e->validator);

            $target->setProperty(
                    'messages',
                    $messages->merge(
                            $this->errorBagExcept($ruleKeysForField)
                    )
            );

            throw $e;
        }

        $this->resetErrorBag($ruleKeysForField);

        return $result;
    }

    protected function shortenModelAttributes($data, $rules, $validator) {
        // If a model ($foo) is a property, and the validation rule is
        // "foo.bar", then set the attribute to just "bar", so that
        // the validation message is shortened and more readable.
        foreach ($rules as $key => $value) {
            $propertyName = $this->beforeFirstDot($key);

            if ($data[$propertyName] instanceof Model) {
                if (str($key)->replace('_', ' ')->is($validator->getDisplayableAttribute($key))) {
                    $validator->addCustomAttributes([$key => $validator->getDisplayableAttribute($this->afterFirstDot($key))]);
                }
            }
        }
    }

    protected function providedOrGlobalRulesMessagesAndAttributes($rules, $messages, $attributes) {
        $rules = is_null($rules) ? $this->getRules() : $rules;

        c::throwIf(empty($rules), new CComponent_Exception_MissingRulesException($this::getName()));

        $messages = empty($messages) ? $this->getMessages() : $messages;
        $attributes = empty($attributes) ? $this->getValidationAttributes() : $attributes;

        return [$rules, $messages, $attributes];
    }

    protected function getDataForValidation($rules) {
        $properties = $this->getPublicPropertiesDefinedBySubClass();

        c::collect($rules)->keys()
                ->each(function ($ruleKey) use ($properties) {
                    $propertyName = $this->beforeFirstDot($ruleKey);

                    c::throwUnless(array_key_exists($propertyName, $properties), new \Exception('No property found for validation: [' . $ruleKey . ']'));
                });

        return c::collect($properties)->map(function ($value) {
                    if ($value instanceof CCollection || $value instanceof CModel_Collection)
                        return $value->toArray();

                    return $value;
                })->all();
    }

    protected function prepareForValidation($attributes) {
        return $attributes;
    }

}
