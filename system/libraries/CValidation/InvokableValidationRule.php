<?php

class CValidation_InvokableValidationRule implements CValidation_RuleInterface, CValidation_Contract_ValidatorAwareRuleInterface {
    /**
     * The invokable that validates the attribute.
     *
     * @var \CValidation_Contract_ValidationRuleInterface|\CValidation_Contract_InvokableRuleInterface
     */
    protected $invokable;

    /**
     * Indicates if the validation invokable failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * The validation error messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * The current validator.
     *
     * @var \CValidation_Validator
     */
    protected $validator;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Create a new explicit Invokable validation rule.
     *
     * @param \CValidation_Contract_ValidationRuleinterface|\CValidation_Contract_InvokableRuleInterface $invokable
     *
     * @return void
     */
    protected function __construct($invokable) {
        $this->invokable = $invokable;
    }

    /**
     * Create a new implicit or explicit Invokable validation rule.
     *
     * @param \CValidation_Contract_ValidationRuleInterface|\Illuminate\Contracts\Validation\InvokableRule $invokable
     *
     * @return \CValidation_RuleInterface
     */
    public static function make($invokable) {
        if ($invokable->implicit ?? false) {
            return new class($invokable) extends CValidation_InvokableValidationRule implements CValidation_RuleImplicitInterface {
            };
        }

        return new CValidation_InvokableValidationRule($invokable);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value) {
        $this->failed = false;

        if ($this->invokable instanceof CValidation_Contract_DataAwareRuleInterface) {
            $this->invokable->setData($this->validator->getData());
        }

        if ($this->invokable instanceof CValidation_Contract_ValidatorAwareRuleInterface) {
            $this->invokable->setValidator($this->validator);
        }

        $method = $this->invokable instanceof CValidation_Contract_ValidationRuleInterface
                        ? 'validate'
                        : '__invoke';

        $this->invokable->{$method}($attribute, $value, function ($attribute, $message = null) {
            $this->failed = true;

            return $this->pendingPotentiallyTranslatedString($attribute, $message);
        });

        return !$this->failed;
    }

    /**
     * Get the underlying invokable rule.
     *
     * @return \CValidation_Contract_ValidationRuleInterface|\CValidation_Contract_InvokableRuleInterface
     */
    public function invokable() {
        return $this->invokable;
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function message() {
        return $this->messages;
    }

    /**
     * Set the data under validation.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the current validator.
     *
     * @param \CValidation_Validator $validator
     *
     * @return $this
     */
    public function setValidator($validator) {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Create a pending potentially translated string.
     *
     * @param string      $attribute
     * @param null|string $message
     *
     * @return \CTranslation_PotentiallyTranslatedString
     */
    protected function pendingPotentiallyTranslatedString($attribute, $message) {
        $destructor = $message === null
            ? fn ($message) => $this->messages[] = $message
            : fn ($message) => $this->messages[$attribute] = $message;

        return new class($message ?? $attribute, $this->validator->getTranslator(), $destructor) extends CTranslation_PotentiallyTranslatedString {
            /**
             * The callback to call when the object destructs.
             *
             * @var \Closure
             */
            protected $destructor;

            /**
             * Create a new pending potentially translated string.
             *
             * @param string                            $message
             * @param \CTranslation_TranslatorInterface $translator
             * @param \Closure                          $destructor
             */
            public function __construct($message, $translator, $destructor) {
                parent::__construct($message, $translator);

                $this->destructor = $destructor;
            }

            /**
             * Handle the object's destruction.
             *
             * @return void
             */
            public function __destruct() {
                ($this->destructor)($this->toString());
            }
        };
    }
}
